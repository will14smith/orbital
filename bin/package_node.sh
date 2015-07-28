#!/usr/bin/env bash

pushd src/node > /dev/null

echo "Creating list of files"

FILELIST=$(mktemp /tmp/orbital.XXXXXXXX)

echo "" > $FILELIST

for filename in *.js; do
  echo $filename >> $FILELIST
done
echo "package.json" >> $FILELIST

# build package
echo "Creating package"

VERSION=$(git rev-parse --short HEAD)
OUTPUT="node-$VERSION.tar.gz"

mkdir -p ../../packages
tar cjf ../../packages/$OUTPUT -T $FILELIST

echo
echo "Created package at $OUTPUT"

# cleanup
rm $FILELIST

popd > /dev/null