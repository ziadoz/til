#!/usr/bin/env bash

screencapture -R$(osascript -e 'tell app "Google Chrome" to get the bounds of the front window' | tr -d '[:space:]') -C -o -x -k -v ~/Desktop/chrome.mov