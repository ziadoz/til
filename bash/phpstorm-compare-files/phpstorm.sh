#!/usr/bin/env bash

# @see: https://www.jetbrains.com/help/phpstorm/working-with-the-ide-features-from-command-line.html
# @see: https://www.jetbrains.com/help/phpstorm/comparing-files-and-folders.html#comparing_folders
# Usage: phpstorm diff [a] [b]
# View > Compare With

open -na "PhpStorm.app" --args "$@"