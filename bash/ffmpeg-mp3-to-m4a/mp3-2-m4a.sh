#!/usr/bin/env bash

for file in *.mp3; do ffmpeg -i $file -c:a aac -vn ${file%.mp3}.m4a; done