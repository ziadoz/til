#!/usr/bin/env bash

# Slugify a string
# @see: https://duncanlock.net/blog/2021/06/15/good-simple-bash-slugify-function/
# @see: https://gist.github.com/oneohthree/f528c7ae1e701ad990e6
function slugify() {
  iconv -t ascii//TRANSLIT \
    | tr -d "'" \
    | sed -E 's/[^a-zA-Z0-9]+/-/g' \
    | sed -E 's/^-+|-+$//g' \
    | tr "[:upper:]" "[:lower:]"
}

# Screenshot and snapshot using Google Chrome
# @see: https://til.simonwillison.net/chrome/headless
# @see: https://stackoverflow.com/questions/13158083/take-a-full-page-screenshot-with-firefox-on-the-command-line
function screenshot() {
    /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --headless=new --window-size=2560x1440 --screenshot="$HOME/Desktop/${2:-$(echo "$1" | slugify).png}" "$1"
}

# SingleFile works better than both this and Monolith, as it offlines all the external content (JS, CSS, images etc.)
function snapshot() {
    /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --headless=new --dump-dom "$1" > "$HOME/Desktop/${2:-$(echo "$1" | slugify).html}"
}

function pdf() {
    /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --headless=new --print-to-pdf="$HOME/Desktop/${2:-$(echo "$1" | slugify).pdf}" "$1"
}