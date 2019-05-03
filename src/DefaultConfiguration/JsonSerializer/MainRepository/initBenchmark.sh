#!/usr/bin/env bash

set -e

# add commands to initialize benchmark: clear cache and logs, warm up cache etc

# --ansi to have colors when this script is called in PHP
composer install --no-dev --classmap-authoritative --ansi
