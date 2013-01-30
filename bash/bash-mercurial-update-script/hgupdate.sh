#!/bin/bash
# Usage: ./hgupdate.sh /path/to/repositories

# Trap Ctrl + C command and exit the script.
trap 'exit 1' 2

# Strip trailing slashes from argument and test it's a directory.
TARGET=${1%/}
if [[ ! -d $TARGET ]]; then
    echo "Please pass in a directory path."
    exit 1
fi

# Store working directory and directories to update.
pwd=$(pwd)
dirs=($(find $TARGET -mindepth 1 -maxdepth 1 -type d))

echo "Update all Mercurial Repositories:"
echo

# Update each directory that contains a Mercurial .hg file.
for dir in "${dirs[@]}"; do
    cd $dir
    if [ -e ".hg" ]; then
        echo "Updating $dir:"
        hg pull -u
        echo
    fi
done

# Switch back to working directory.
cd $pwd