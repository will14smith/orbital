#!/usr/bin/env sh

sed -i -e 's/user:.*/user: travis/' app/config/config_test.yml
sed -i -e 's/password:.*/password: ~/' app/config/config_test.yml
