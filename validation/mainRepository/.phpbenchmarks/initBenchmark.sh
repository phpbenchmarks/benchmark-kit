#!/usr/bin/env bash

function initBenchmark {
    # add commands to initialize benchmark: clear cache and logs, warmp up cache etc

    composer install --no-dev --classmap-authoritative
    [ $? != "0" ] && exit 1

    return 0
}
