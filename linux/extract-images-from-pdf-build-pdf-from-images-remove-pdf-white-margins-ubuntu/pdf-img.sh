#!/usr/bin/env bash

# Extract images from a PDF: https://askubuntu.com/questions/117143/command-line-tool-to-bulk-extract-images-from-a-pdf
sudo apt-get install poppler-utils
pdfimages -all input.pdf images/image

# Build PDF from images: https://stackoverflow.com/questions/8955425/how-can-i-convert-a-series-of-images-to-a-pdf-from-the-command-line-on-linux
sudo apt install img2pdf
img2pdf --output d.pdf images/image*.jpg

# Extracting the images from a PDF and then rebuilding it removes white margins surrounding images.