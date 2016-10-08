#!/usr/bin/env bash

# THIS MUST BE RUN as bin/package_web.sh

# build assets
echo "Building assets"

pushd assets > /dev/null

npm install
gulp

popd > /dev/null

# build bundle assets
echo "Building bundle assets"
app/console assets:install

# build file list
echo "Creating list of files"

FILELIST=$(mktemp /tmp/orbital.XXXXXXXX)

echo "" > $FILELIST

echo "app/config/config.yml" >> $FILELIST
echo "app/config/config_prod.yml" >> $FILELIST
echo "app/config/parameters.yml.dist" >> $FILELIST
echo "app/config/routing.yml" >> $FILELIST
echo "app/config/security.yml" >> $FILELIST
echo "app/config/services.yml" >> $FILELIST
echo "app/config/version.yml" >> $FILELIST
echo "app/DoctrineMigrations/" >> $FILELIST
echo "app/Resources/" >> $FILELIST
echo "app/AppCache.php" >> $FILELIST
echo "app/AppKernel.php" >> $FILELIST
echo "app/autoload.php" >> $FILELIST
echo "app/console" >> $FILELIST
echo "src/AppBundle/" >> $FILELIST
echo "web/bundles/" >> $FILELIST
echo "web/css/" >> $FILELIST
echo "web/fonts/" >> $FILELIST
echo "web/images/" >> $FILELIST
echo "web/js/" >> $FILELIST
echo "web/app.php" >> $FILELIST
echo "web/favicon.ico" >> $FILELIST
echo "web/robots.txt" >> $FILELIST
echo "composer.json" >> $FILELIST
echo "composer.lock" >> $FILELIST

# build package
echo "Creating package"

VERSION=$(git rev-parse --short HEAD)
VERSION_DATE=$(date -d "$(git show -s --format=%ai HEAD)" +"%y%m%d%H%M")
OUTPUT="web-$VERSION.tar.gz"

# set version
echo "parameters:" > app/config/version.yml
echo "  orbital.version: $VERSION" >> app/config/version.yml
echo "  orbital.version.date: $VERSION_DATE" >> app/config/version.yml

mkdir -p packages
tar cjf packages/$OUTPUT -T $FILELIST

echo
echo "Created package at $OUTPUT"

# cleanup
rm $FILELIST
echo "parameters:" > app/config/version.yml
echo "  orbital.version: master" >> app/config/version.yml
echo "  orbital.version.date: $(date +"%y%m%d%H%M")" >> app/config/version.yml