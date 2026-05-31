package main

import (
	"bytes"
	"database/sql"
	"encoding/csv"
	"encoding/xml"
	"flag"
	"fmt"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"time"

	_ "modernc.org/sqlite"
)

type Tab struct {
	Source    string
	Device    string
	Title     string
	URL       string
	DateAdded string
	Read      string
}

// --- Plist XML parser ---

type plistDict = map[string]any
type plistArray = []any

func parsePlistFile(path string) (plistDict, error) {
	data, err := exec.Command("plutil", "-convert", "xml1", "-o", "-", path).Output()
	if err != nil {
		return nil, fmt.Errorf("plutil: %w", err)
	}

	d := xml.NewDecoder(bytes.NewReader(data))

	for {
		tok, err := d.Token()
		if err != nil {
			return nil, fmt.Errorf("plist: finding root: %w", err)
		}
		se, ok := tok.(xml.StartElement)
		if !ok || se.Name.Local != "plist" {
			continue
		}
		// consume the dict directly inside <plist>
		for {
			tok, err = d.Token()
			if err != nil {
				return nil, err
			}
			if se, ok := tok.(xml.StartElement); ok {
				val, err := parsePlistValue(d, se)
				if err != nil {
					return nil, err
				}
				if dict, ok := val.(plistDict); ok {
					return dict, nil
				}
			}
		}
	}
}

func parsePlistValue(d *xml.Decoder, start xml.StartElement) (any, error) {
	switch start.Name.Local {
	case "dict":
		return parsePlistDict(d)
	case "array":
		return parsePlistArray(d)
	case "string", "date":
		var s string
		return s, d.DecodeElement(&s, &start)
	case "integer":
		var i int64
		return i, d.DecodeElement(&i, &start)
	case "real":
		var f float64
		return f, d.DecodeElement(&f, &start)
	case "true":
		return true, d.Skip()
	case "false":
		return false, d.Skip()
	default:
		// data blobs and unknown types — skip
		return nil, d.Skip()
	}
}

func parsePlistDict(d *xml.Decoder) (plistDict, error) {
	result := plistDict{}
	var key string
	for {
		tok, err := d.Token()
		if err != nil {
			return nil, err
		}
		switch v := tok.(type) {
		case xml.StartElement:
			if v.Name.Local == "key" {
				d.DecodeElement(&key, &v)
			} else {
				val, err := parsePlistValue(d, v)
				if err != nil {
					return nil, err
				}
				result[key] = val
				key = ""
			}
		case xml.EndElement:
			return result, nil
		}
	}
}

func parsePlistArray(d *xml.Decoder) (plistArray, error) {
	var result plistArray
	for {
		tok, err := d.Token()
		if err != nil {
			return nil, err
		}
		switch v := tok.(type) {
		case xml.StartElement:
			val, err := parsePlistValue(d, v)
			if err != nil {
				return nil, err
			}
			result = append(result, val)
		case xml.EndElement:
			return result, nil
		}
	}
}

func findPlistNode(node plistDict, title string) plistDict {
	if t, _ := node["Title"].(string); t == title {
		return node
	}
	for _, v := range node {
		switch val := v.(type) {
		case plistDict:
			if result := findPlistNode(val, title); result != nil {
				return result
			}
		case plistArray:
			for _, item := range val {
				if dict, ok := item.(plistDict); ok {
					if result := findPlistNode(dict, title); result != nil {
						return result
					}
				}
			}
		}
	}
	return nil
}

// --- Date helpers ---

var appleEpoch = time.Date(2001, 1, 1, 0, 0, 0, 0, time.UTC)

const layout = "2006-01-02 15:04:05"

func formatISO(s string) string {
	if s == "" {
		return ""
	}
	t, err := time.Parse(time.RFC3339, s)
	if err != nil {
		return s
	}
	return t.UTC().Format(layout)
}

func formatAppleTS(ts float64) string {
	if ts == 0 {
		return ""
	}
	return appleEpoch.Add(time.Duration(ts * float64(time.Second))).Format(layout)
}

// --- Data sources ---

func readingList(path string) ([]Tab, error) {
	data, err := parsePlistFile(path)
	if err != nil {
		return nil, err
	}

	node := findPlistNode(data, "com.apple.ReadingList")
	if node == nil {
		return nil, nil
	}

	children, _ := node["Children"].(plistArray)
	tabs := make([]Tab, 0, len(children))

	for _, child := range children {
		item, ok := child.(plistDict)
		if !ok {
			continue
		}

		url, _ := item["URLString"].(string)
		uriDict, _ := item["URIDictionary"].(plistDict)
		title, _ := uriDict["title"].(string)
		rl, _ := item["ReadingList"].(plistDict)
		dateAdded, _ := rl["DateAdded"].(string)
		_, fetched := rl["DateLastFetched"]

		tabs = append(tabs, Tab{
			Source:    "reading_list",
			Title:     title,
			URL:       url,
			DateAdded: formatISO(dateAdded),
			Read:      boolStr(fetched),
		})
	}
	return tabs, nil
}

func parseLocalTabsOutput(output string) []Tab {
	var tabs []Tab
	for _, line := range strings.Split(strings.TrimSpace(output), "\n") {
		parts := strings.SplitN(line, "|||", 2)
		if len(parts) != 2 {
			continue
		}
		title, url := parts[0], parts[1]
		if url == "" || url == "favorites://" {
			continue
		}
		tabs = append(tabs, Tab{
			Source: "local_tab",
			Device: "this mac",
			Title:  title,
			URL:    url,
		})
	}
	return tabs
}

func localTabs() ([]Tab, error) {
	script := `tell application "Safari"
		set output to ""
		repeat with w in windows
			repeat with t in tabs of w
				set output to output & (name of t) & "|||" & (URL of t) & linefeed
			end repeat
		end repeat
		return output
	end tell`

	out, err := exec.Command("osascript", "-e", script).Output()
	if err != nil {
		return nil, fmt.Errorf("osascript: %w", err)
	}
	return parseLocalTabsOutput(string(out)), nil
}

func icloudTabs(dbPath string) ([]Tab, error) {
	db, err := sql.Open("sqlite", "file:"+dbPath+"?mode=ro")
	if err != nil {
		return nil, err
	}
	defer db.Close()

	rows, err := db.Query(`
		SELECT t.title, t.url, t.last_viewed_time, d.device_name
		FROM cloud_tabs t
		LEFT JOIN cloud_tab_devices d ON t.device_uuid = d.device_uuid
		WHERE t.url IS NOT NULL AND t.url != ''
		ORDER BY d.device_name, t.last_viewed_time DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var tabs []Tab
	for rows.Next() {
		var title, url, device sql.NullString
		var ts sql.NullFloat64
		if err := rows.Scan(&title, &url, &ts, &device); err != nil {
			return nil, err
		}
		tabs = append(tabs, Tab{
			Source:    "icloud_tab",
			Device:    device.String,
			Title:     title.String,
			URL:       url.String,
			DateAdded: formatAppleTS(ts.Float64),
		})
	}
	return tabs, rows.Err()
}

func boolStr(b bool) string {
	if b {
		return "yes"
	}
	return "no"
}

// --- CSV output ---

func writeCSV(path string, tabs []Tab) error {
	f, err := os.Create(path)
	if err != nil {
		return err
	}
	defer f.Close()

	w := csv.NewWriter(f)
	w.Write([]string{"source", "device", "title", "url", "date_added", "read"})
	for _, tab := range tabs {
		w.Write([]string{tab.Source, tab.Device, tab.Title, tab.URL, tab.DateAdded, tab.Read})
	}
	w.Flush()
	return w.Error()
}

// --- Main ---

func main() {
	home, _ := os.UserHomeDir()

	output := flag.String("o", filepath.Join(home, "Desktop", "safari_export.csv"), "path to write the CSV output")
	flag.Parse()

	bookmarksPath := filepath.Join(home, "Library/Safari/Bookmarks.plist")
	cloudTabsPath := filepath.Join(home, "Library/Containers/com.apple.Safari/Data/Library/Safari/CloudTabs.db")

	fmt.Println("Exporting Safari data...")

	var all []Tab

	reading, err := readingList(bookmarksPath)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Warning: reading list: %v\n", err)
	}
	fmt.Printf("  Reading list: %d items\n", len(reading))
	all = append(all, reading...)

	local, err := localTabs()
	if err != nil {
		fmt.Fprintf(os.Stderr, "Warning: local tabs: %v\n", err)
	}
	fmt.Printf("  Local tabs:   %d items\n", len(local))
	all = append(all, local...)

	icloud, err := icloudTabs(cloudTabsPath)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Warning: iCloud tabs: %v\n", err)
	}
	fmt.Printf("  iCloud tabs:  %d items\n", len(icloud))
	all = append(all, icloud...)

	if err := writeCSV(*output, all); err != nil {
		fmt.Fprintf(os.Stderr, "Error writing CSV: %v\n", err)
		os.Exit(1)
	}

	fmt.Printf("\nWritten %d rows to %s\n", len(all), *output)
}
