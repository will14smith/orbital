#!/usr/bin/env bash

BASE_INSTALL_PATH=/var/orbital-node

VERSION=$(echo $1 | sed 's/.*node-\([a-f0-9]*\)\.tar\.gz/\1/');
INSTALL_PATH="$BASE_INSTALL_PATH/$VERSION"

# extract package
echo "Extracting package to $INSTALL_PATH"

mkdir -p $INSTALL_PATH
tar xjf $1 -C $INSTALL_PATH
chown -R orbital-node:orbital-node $INSTALL_PATH

pushd $INSTALL_PATH > /dev/null

# install npm
echo "Installing npm packages"

npm install
npm install newrelic

# config
echo "Setting up config symlinks"

cp node_modules/newrelic/newrelic.js newrelic.js.orig
cp config.js config.js.orig

if [ ! -f $BASE_INSTALL_PATH/config.js ]; then
  echo "Creating $BASE_INSTALL_PATH/config.js please configure it."
  cp config.js.orig $BASE_INSTALL_PATH/config.js
fi
if [ ! -f $BASE_INSTALL_PATH/newrelic.js ]; then
  echo "Creating $BASE_INSTALL_PATH/newrelic.js please configure it."
  cp newrelic.js.orig $BASE_INSTALL_PATH/newrelic.js
fi

rm config.js newrelic.js

ln -s $BASE_INSTALL_PATH/config.js config.js
ln -s $BASE_INSTALL_PATH/newrelic.js newrelic.js

popd > /dev/null

# stop old service
echo "Stopping old version"
stop orbital-node

# setup upstart script
echo "Installing new startup script"
cat node_upstart_template | sed -r "s#@@INSTALL_PATH@@#$INSTALL_PATH#g" > /etc/init/orbital-node.conf
chmod 644 /etc/init/orbital-node.conf

echo "Starting new version"
start orbital-node

echo "Setup complete -- make sure config.js & newrelic.js are up to date"