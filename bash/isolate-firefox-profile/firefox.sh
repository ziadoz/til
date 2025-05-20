#!/usr/bin/env bash

PROFILEDIR="$(mktemp -d)"
firefox --no-remote  --profile "$PROFILEDIR" --screenshot $PWD/output.png https://xkcd.com
rm -r "$PROFILEDIR"