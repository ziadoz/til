//go:generate sqlite3 testdata/cloud_tabs.db < testdata/cloud_tabs.sql

package main

import (
	"os"
	"path/filepath"
	"strings"
	"testing"

	_ "modernc.org/sqlite"
)

func TestFormatISO(t *testing.T) {
	tests := []struct {
		input string
		want  string
	}{
		{"2026-05-24T21:55:18Z", "2026-05-24 21:55:18"},
		{"2026-05-23T10:00:00Z", "2026-05-23 10:00:00"},
		{"", ""},
		{"not-a-date", "not-a-date"}, // passthrough on parse error
	}
	for _, tc := range tests {
		if got := formatISO(tc.input); got != tc.want {
			t.Errorf("formatISO(%q) = %q, want %q", tc.input, got, tc.want)
		}
	}
}

func TestFormatAppleTS(t *testing.T) {
	if got := formatAppleTS(0); got != "" {
		t.Errorf("formatAppleTS(0) = %q, want empty string", got)
	}

	// 775825472 seconds after 2001-01-01 UTC = 2025-08-04
	got := formatAppleTS(775825472.0)
	if !strings.HasPrefix(got, "2025-") {
		t.Errorf("formatAppleTS(775825472) = %q, expected a 2025 date", got)
	}
}

func TestParseLocalTabsOutput(t *testing.T) {
	input := strings.Join([]string{
		"Example Article|||https://example.com/article",
		"GitHub|||https://github.com",
		"Start Page|||favorites://",
		"no separator here",
		"Empty URL|||",
	}, "\n")

	tabs := parseLocalTabsOutput(input)

	if len(tabs) != 2 {
		t.Fatalf("expected 2 tabs, got %d", len(tabs))
	}
	if tabs[0].Title != "Example Article" || tabs[0].URL != "https://example.com/article" {
		t.Errorf("unexpected tab[0]: %+v", tabs[0])
	}
	if tabs[0].Source != "local_tab" || tabs[0].Device != "this mac" {
		t.Errorf("unexpected source/device on tab[0]: %+v", tabs[0])
	}
	if tabs[1].URL != "https://github.com" {
		t.Errorf("unexpected tab[1] URL: %q", tabs[1].URL)
	}
}

func TestFindPlistNode(t *testing.T) {
	data := plistDict{
		"Title": "root",
		"Children": plistArray{
			plistDict{"Title": "BookmarksBar"},
			plistDict{
				"Title":    "com.apple.ReadingList",
				"Children": plistArray{},
			},
		},
	}

	result := findPlistNode(data, "com.apple.ReadingList")
	if result == nil {
		t.Fatal("expected to find reading list node, got nil")
	}
	if result["Title"] != "com.apple.ReadingList" {
		t.Errorf("found node has wrong title: %v", result["Title"])
	}
	if findPlistNode(data, "nonexistent") != nil {
		t.Error("expected nil for nonexistent node title")
	}
}

func TestReadingList(t *testing.T) {
	tabs, err := readingList("testdata/bookmarks.plist")
	if err != nil {
		t.Fatalf("readingList: %v", err)
	}
	if len(tabs) != 2 {
		t.Fatalf("expected 2 items, got %d", len(tabs))
	}

	unread := tabs[0]
	if unread.Source != "reading_list" {
		t.Errorf("source = %q, want reading_list", unread.Source)
	}
	if unread.URL != "https://example.com/article" {
		t.Errorf("URL = %q, want https://example.com/article", unread.URL)
	}
	if unread.Title != "Example Article" {
		t.Errorf("title = %q, want Example Article", unread.Title)
	}
	if unread.DateAdded != "2026-05-24 21:55:18" {
		t.Errorf("date_added = %q, want 2026-05-24 21:55:18", unread.DateAdded)
	}
	if unread.Read != "no" {
		t.Errorf("read = %q, want no", unread.Read)
	}

	read := tabs[1]
	if read.Read != "yes" {
		t.Errorf("read = %q, want yes (item has DateLastFetched)", read.Read)
	}
}

func TestICloudTabs(t *testing.T) {
	tabs, err := icloudTabs("testdata/cloud_tabs.db")
	if err != nil {
		t.Fatalf("icloudTabs: %v", err)
	}

	// tab-4 has empty URL so should be filtered out
	if len(tabs) != 3 {
		t.Fatalf("expected 3 tabs, got %d", len(tabs))
	}
	for _, tab := range tabs {
		if tab.Source != "icloud_tab" {
			t.Errorf("source = %q, want icloud_tab", tab.Source)
		}
		if tab.URL == "" {
			t.Error("got tab with empty URL")
		}
		if tab.DateAdded == "" {
			t.Errorf("got empty DateAdded for tab %q", tab.URL)
		}
	}

	// Results are ordered by device_name then last_viewed_time DESC
	// iPad: Apple; iPhone: Go Blog, GitHub
	if tabs[0].Device != "iPad" {
		t.Errorf("tab[0].Device = %q, want iPad", tabs[0].Device)
	}
	if tabs[1].Device != "iPhone" || tabs[1].URL != "https://go.dev/blog" {
		t.Errorf("tab[1] = %+v, want iPhone / go.dev/blog", tabs[1])
	}
}

func TestWriteCSV(t *testing.T) {
	path := filepath.Join(t.TempDir(), "out.csv")

	tabs := []Tab{
		{Source: "reading_list", Title: "Test Article", URL: "https://example.com", DateAdded: "2026-01-01 00:00:00", Read: "no"},
		{Source: "local_tab", Device: "this mac", Title: "GitHub", URL: "https://github.com"},
		{Source: "icloud_tab", Device: "iPhone", Title: `Title with "quotes"`, URL: "https://example.com/quoted"},
	}

	if err := writeCSV(path, tabs); err != nil {
		t.Fatalf("writeCSV: %v", err)
	}

	data, err := os.ReadFile(path)
	if err != nil {
		t.Fatal(err)
	}

	content := string(data)
	if !strings.Contains(content, "source,device,title,url,date_added,read") {
		t.Error("CSV missing header row")
	}
	if !strings.Contains(content, "https://example.com") {
		t.Error("CSV missing expected URL")
	}
	if !strings.Contains(content, `"Title with ""quotes"""`) {
		t.Error("CSV did not correctly escape quoted title")
	}
}
