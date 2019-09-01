// Usage: go run main.go https://www.theguardian.com/uk
// Based on Monolith: https://github.com/Y2Z/monolith
package main

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"io"
	"io/ioutil"
	"log"
	"net/http"
	"net/url"
	"os"
	"strings"
	"sync"

	"github.com/gocolly/colly"
	"github.com/gosimple/slug"
)

func main() {
	for _, link := range os.Args[1:] {
		DownloadWebpage(link)
	}
}

func DownloadWebpage(link string) {
	filename := MakeFilename(link)
	collector := colly.NewCollector()

	collector.OnRequest(func(r *colly.Request) {
		fmt.Println("Visiting", r.URL.String())
	})

	collector.OnResponse(func(r *colly.Response) {
		err := r.Save(filename)
		if err != nil {
			log.Fatal(err)
		}
		fmt.Println("File saved to " + filename)
	})

	collector.OnHTML("img[src], script[src], source[src]", func(e *colly.HTMLElement) {
		src := e.Request.AbsoluteURL(e.Attr("src"))
		fmt.Println("Downloading asset " + src)
		ReplaceAsset(filename, e.Attr("src"), DownloadBase64Asset(src))
	})

	collector.OnHTML("link[href]", func(e *colly.HTMLElement) {
		rel := e.Attr("rel")
		if rel == "preconnect" || rel == "dns-prefetch" || rel == "alternate" || rel == "publisher" {
			return
		}

		href := e.Request.AbsoluteURL(e.Attr("href"))
		fmt.Println("Downloading asset " + href)
		ReplaceAsset(filename, e.Attr("href"), DownloadBase64Asset(href))
	})

	collector.OnHTML("soure[srcset]", func(e *colly.HTMLElement) {
		srcset := e.Request.AbsoluteURL(e.Attr("srcset"))
		fmt.Println("Downloading asset " + srcset)
		ReplaceAsset(filename, e.Attr("srcset"), DownloadBase64Asset(srcset))
	})

	collector.Visit(link)
}

// https://stackoverflow.com/questions/49135097/base64-encode-io-reader
func DownloadBase64Asset(link string) string {
	resp, err := http.Get(link)
	if err != nil {
		log.Fatal(err)
	}

	defer resp.Body.Close()

	pr, pw := io.Pipe()
	encoder := base64.NewEncoder(base64.StdEncoding, pw)

	var wg sync.WaitGroup
	wg.Add(1)

	go func() {
		defer wg.Done()

		_, err := io.Copy(encoder, resp.Body)
		encoder.Close()

		if err != nil {
			pw.CloseWithError(err)
		} else {
			pw.Close()
		}
	}()

	var buf bytes.Buffer
	buf.ReadFrom(pr)

	return "data:" + resp.Header.Get("Content-Type") + ";base64," + buf.String()
}

func ReplaceAsset(filename string, find string, replace string) {
	read, err := ioutil.ReadFile(filename)
	if err != nil {
		log.Fatal(err)
	}

	replaced := strings.Replace(string(read), find, replace, -1)

	err = ioutil.WriteFile(filename, []byte(replaced), 0)
	if err != nil {
		log.Fatal(err)
	}
}

// https://gist.github.com/tdegrunt/045f6b3377f3f7ffa408
func MakeFilename(link string) string {
	parts, err := url.Parse(link)
	if err != nil {
		log.Fatal(err)
	}

	return slug.Make(strings.Replace(link, parts.Scheme+"://", "", 1)) + ".html"
}
