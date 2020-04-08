#!/usr/bin/env bash
alias icloud_tabs='sqlite3 -header -csv ~/Library/Safari/CloudTabs.db "select ctd.device_name, ct.title, ct.url from cloud_tabs as ct inner join cloud_tab_devices as ctd on ctd.device_uuid = ct.device_uuid order by device_name asc, position asc"'

# Output to CSV file:
icloud_tabs > icloud_tabs.csv

# Unique CSV:
sort -u icloud_tabs.csv -o icloud_tabs_unique.csv

# Merge CSVs:
cat icloud_tabs_1.csv icloud_tabs_2.csv > icloud_tabs_merged.csv