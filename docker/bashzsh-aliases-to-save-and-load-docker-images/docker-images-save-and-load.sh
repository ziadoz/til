#!/usr/bin/env bash

function save-docker-images() {
    DEST="${1:-.}"
    mkdir -p "$DEST"

    echo "Saving Docker Images:"

    for IMAGE in $(docker images --format "{{.Repository}}"); do
        docker save "$IMAGE" > "$DEST/$(echo "$IMAGE" | sed -E 's~:|/~_~g').tar"
        echo "Saved image $IMAGE"
    done
}

function load-docker-images() {
    SOURCE="${1:-.}"

    echo "Loading Docker Images:"

    for image in $(find "$SOURCE" -type f -name "*.tar"); do
        docker load --input $image
    done
}
