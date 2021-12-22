#!/usr/bin/env bash

for file in `find templates -name "*.oldext" -type f -print`; do
    git mv "$file" ${file%.oldext}.newext;
done