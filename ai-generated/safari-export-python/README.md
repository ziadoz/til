# safari-export (Python)

Exports Safari reading list, local open tabs, and iCloud tabs to a CSV file. A
single, standard-library-only Python script (no third-party dependencies).

See also the [Go](../safari-export-go/) and [PHP](../safari-export-php/)
implementations, which produce the same CSV.

## Requirements

- macOS with Safari
- Python 3
- Terminal app must have **Full Disk Access** (System Settings > Privacy &
  Security > Full Disk Access), otherwise Safari's library files are
  permission-denied. Sources that can't be read are skipped with a warning.

## Usage

```
python3 safari_export.py [-o FOLDER]
```

By default the CSV is written to `~/Desktop/safari_export.csv`. Use `-o` to pick
a different folder (it's created if missing):

```
python3 safari_export.py -o ~/Documents
```

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
