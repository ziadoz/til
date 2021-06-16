# Generate Random Files
# @see: https://support.google.com/elastifile-support/answer/9899027?hl=en
# Usage: ./random-files.sh -d /path/to/ouput -s 10 -m 20 - l 5
# Large: <2GB
# Medium: <200MB, <8MB each
# Small: <1MB

DEST="$PWD"
SMALL=0
MEDIUM=0
LARGE=0

while getopts "d:s:m:l:" flag; do
    case "${flag}" in
        d) DEST=${OPTARG};;
        s) SMALL=${OPTARG};;
        m) MEDIUM=${OPTARG};;
        l) LARGE=${OPTARG};;
    esac
done

if [ -z "$DEST" ] || [ ! -d "$DEST" ]; then
    echo "Error: destination (-d) '$DEST' must exist" >&2
    exit 1
fi

if [ $LARGE -gt 0 ]; then
    echo "Making $LARGE large files..."
    mkdir -p "$DEST/large-files"

    for n in $(seq 1 $LARGE); do
        dd if=/dev/urandom of="$DEST/large-files/file$( printf "%03d" $n ).bin" bs=64k count=$(( RANDOM + 1024 )) > /dev/null 2>&1;
    done
fi

if [ $MEDIUM -gt 0 ]; then
    echo "Making $MEDIUM medium files..."
    mkdir -p "$DEST/medium-files"

    for n in $(seq 1 $MEDIUM); do
        dd if=/dev/urandom of="$DEST/medium-files/file$( printf "%03d" $n ).bin" bs=8k count=$(( RANDOM + 1024 )) > /dev/null 2>&1;
    done

    for n in $(seq 1 $MEDIUM); do
        dd if=/dev/urandom of="$DEST/medium-files/file$( printf "%03d" $n ).bin" bs=4k count=$(( RANDOM +1024 )) > /dev/null 2>&1;
    done
fi

if [ $SMALL -gt 0 ]; then
    echo "Making $SMALL small files..."
    mkdir -p "$DEST/small-files"

    for n in $(seq 1 $SMALL); do
        dd if=/dev/urandom of="$DEST/small-files/file$( printf "%03d" $n ).bin" bs=1 count=$(( RANDOM + 1024 )) > /dev/null 2>&1;
    done
fi
