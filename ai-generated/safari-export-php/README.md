# safari-export (PHP)

A dependency-free PHP take on the Safari exporter. It reads three sources and
writes them to one CSV: the reading list (from `Bookmarks.plist`), the open
tabs in running Safari windows (via AppleScript), and tabs synced from your
other devices over iCloud (from `CloudTabs.db`).

It leans on PHP 8.2 language features (`enum`, `readonly` classes, named
arguments) and built-in extensions only: `DOM` to walk the property list that
`plutil` converts to XML, and `PDO` + `pdo_sqlite` to read the iCloud tab
database. No Composer, no autoloader, just one file.

## Running it

```
php safari_export.php
```

It always writes to `~/Desktop/safari_export.csv`. There is no flag to change
the destination here. Reach for the [Python](../safari-export-python/) version
if you want a configurable output folder, or the
[Go](../safari-export-go/) version for a compiled binary.

Your terminal needs Full Disk Access (System Settings > Privacy & Security)
before it can read the reading list and iCloud databases. Without it, those two
sources warn on stderr and the script carries on with whatever it could reach.

## What lands in the CSV

Six columns per row: `source` (one of `reading_list`, `local_tab`,
`icloud_tab`), `device` (the syncing device for iCloud tabs, `this mac` for
local ones), `title`, `url`, `date_added` (when the reading-list item was saved,
or when the iCloud tab was last viewed), and `read` (`yes`/`no`, set only for
reading-list items based on whether Safari fetched the full article).
