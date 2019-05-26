// Usage: game-save-clean-up  --src="/Files/Game Saves" [--limit=2] [--dryrun]
package main

import (
	"flag"
	"fmt"
	"io/ioutil"
	"log"
	"os"
	"path/filepath"
	"sort"
)

var src string
var limit int
var dryrun bool

func main() {
	log.SetFlags(0)

	flag.StringVar(&src, "src", "./", "Source directory")
	flag.IntVar(&limit, "limit", 5, "Number of files to keep")
	flag.BoolVar(&dryrun, "dryrun", false, "Perform a dry run")
	flag.Parse()

	dirs, err := ioutil.ReadDir(src)
	if err != nil {
		log.Fatal(err)
	}

	for _, dir := range dirs {
		// Identify game save files.
		files := []os.FileInfo{}

		filepath.Walk(fmt.Sprintf("%s/%s", src, dir.Name()), func(path string, info os.FileInfo, err error) error {
			if !info.IsDir() {
				files = append(files, info)
			}
			return nil
		})

		// Skip if not enough files.
		if len(files) <= limit {
			continue
		}

		// Sort newest to oldest.
		sort.Slice(files, func(i, j int) bool {
			return files[j].ModTime().Unix() < files[i].ModTime().Unix()
		})

		// List out what we found.
		fmt.Printf("Found %d %s save games: \n", len(files), dir.Name())

		for _, file := range files {
			fmt.Println("  - " + file.Name())
		}

		// Perform clean up file deletion
		deletes := files[limit:]

		if dryrun {
			fmt.Printf("Dry deleting %d save games: \n", len(deletes))

			for _, file := range deletes {
				fmt.Println("  - " + file.Name())
			}
		} else {
			fmt.Printf("Deleting %d save games: \n", len(deletes))

			for _, file := range deletes {
				if err := os.Remove(fmt.Sprintf("%s/%s/%s", src, dir.Name(), file.Name())); err != nil {
					log.Fatal(err)
				}

				fmt.Println("  - " + file.Name())
			}
		}
	}

	fmt.Println("Done!")
}
