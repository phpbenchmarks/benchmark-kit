#!/usr/bin/env bash

# add commands to initialize benchmark: clear cache and logs, warm up cache etc

# --ansi to have colors when this script is called in PHP
composer install --no-dev --classmap-authoritative --ansi
[ $? != "0" ] && exit 1
