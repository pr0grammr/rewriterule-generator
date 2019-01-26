#!/usr/bin/env bash

# install dependencies
echo "install composer dependencies"
composer install

# start server
echo "starting symfony dev server"
bin/console server:run
