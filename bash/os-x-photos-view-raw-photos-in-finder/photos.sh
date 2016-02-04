#!/usr/bin/env bash

# Make the OS X Photos Library viewable as a folder called Raw in ~/Pictures
cd ~/Pictures
mkdir Raw
ln -s ~/Pictures/Photos\ Library.photoslibrary/Masters/ Raw