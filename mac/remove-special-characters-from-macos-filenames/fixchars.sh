#!/usr/bin/env bash

# @see: https://superuser.com/questions/617517/remove-all-illegal-characters-from-all-filenames-in-a-given-folder
# The 's/[\:?\\]//g' regular expression removes ?, : and \ characters.

# Dry Run:
find "/path/to/files" -type f -exec rename -n 's/[\:?\\]//g' {} \;

# Execute:
find "/path/to/files" -type f -exec rename 's/[\:?\\]//g' {} \;