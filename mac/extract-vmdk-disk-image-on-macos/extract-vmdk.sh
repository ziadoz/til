#!/usr/bin/env bash

# Unzip Vagrant .box file:
tar -xf <box> -C <destination>

# Install 7 Zip:
brew install p7zip 

# Extract VMDK:
7z x -y -o<destination> <vmdk>