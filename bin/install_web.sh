#!/usr/bin/env bash

BASE_INSTALL_PATH=/var/www/orbital-src
APP_PATH=/var/www/orbital
UPLOAD_PATH=/var/www/orbital-uploads

VERSION=$(echo $1 | sed 's/.*web-\([a-f0-9]*\)\.tar\.gz/\1/');
INSTALL_PATH="$BASE_INSTALL_PATH/$VERSION"

# extract package
echo "Extracting package to $INSTALL_PATH"

mkdir -p $INSTALL_PATH
tar xjf $1 -C $INSTALL_PATH
chown -R www-data:www-data $INSTALL_PATH

# update symlinks -- uploads
echo "Linking uploads folder"

mkdir -p $UPLOAD_PATH
chown www-data:www-data $UPLOAD_PATH
ln -s $UPLOAD_PATH $INSTALL_PATH/web/uploads

pushd $INSTALL_PATH > /dev/null
export SYMFONY_ENV=prod

# setup parameters.yml
if [ ! -f $BASE_INSTALL_PATH/parameters.yml ]; then
  echo "Creating $BASE_INSTALL_PATH/parameters.yml"
  touch $BASE_INSTALL_PATH/parameters.yml
fi

ln -s $BASE_INSTALL_PATH/parameters.yml $INSTALL_PATH/app/config/parameters.yml

# install composer deps
echo "Installing dependencies"

curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader

# warmup cache
echo "Warming up cache"

php app/console cache:clear --env=prod --no-debug
chown -R www-data:www-data $INSTALL_PATH/app/cache/prod $INSTALL_PATH/app/logs/
chmod -R +w $INSTALL_PATH/app/cache/prod $INSTALL_PATH/app/logs/

# update symlinks -- offline
echo "Taking Orbital offline"

OFFLINE_PATH="$BASE_INSTALL_PATH/offline"

rm -R $OFFLINE_PATH
mkdir -p $OFFLINE_PATH/web
echo "<!doctype html><html><title>Orbital Offline</title><body><h1>Orbital is updating...</h1>" > $OFFLINE_PATH/web/app.php
chown -R www-data:www-data $OFFLINE_PATH

rm $APP_PATH
ln -s $OFFLINE_PATH $APP_PATH

service php5-fpm restart

echo "Orbital offline"

# migrate DB
echo "Migrating database"

php app/console doctrine:migrations:migrate

popd > /dev/null

# update symlinks -- app
echo "Putting new version live"

rm $APP_PATH
ln -s $INSTALL_PATH $APP_PATH
chown www-data:www-data $APP_PATH

service php5-fpm restart

echo "Setup complete -- check $BASE_INSTALL_PATH/parameters.yml";