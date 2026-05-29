# safari-export

Exports Safari reading list, local open tabs, and iCloud tabs to a CSV file.

## Requirements

- macOS with Safari
- Terminal app must have **Full Disk Access** (System Settings > Privacy & Security > Full Disk Access), otherwise Safari's library files will be permission-denied

## Output

A single CSV with these columns:

| Column | Description |
|---|---|
| `source` | `reading_list`, `local_tab`, or `icloud_tab` |
| `device` | Device name for iCloud tabs, `this mac` for local tabs |
| `title` | Page title |
| `url` | Page URL |
| `date_added` | Date added (reading list) or last viewed (iCloud tabs) |
| `read` | `yes` / `no` — reading list only, based on whether Safari fetched the full content |

## Usage

```
safari-export [-o output.csv]
```

By default the CSV is written to `~/Desktop/safari_export.csv`. Use `-o` to write it elsewhere:

```
safari-export -o ~/Documents/tabs.csv
```

## Build

```
go build -o safari-export .
```

Or install directly to your PATH:

```
go install .
```

## Test

```
go test ./...
```

The test fixtures live in `testdata/`. To regenerate the SQLite fixture after changing `testdata/cloud_tabs.sql`:

```
rm testdata/cloud_tabs.db && go generate ./...
```

## Release

Releases are built with [GoReleaser](https://goreleaser.com) as a macOS universal binary (arm64 + amd64).

```
brew install goreleaser
goreleaser release --clean
```

A snapshot build (no git tag required):

```
goreleaser build --snapshot --clean
```
