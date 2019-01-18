#!/usr/bin/env bash

function validateCode {
    local containerName=$1

    cd docker/validate

    if [ $VERBOSE_LEVEL -ge 2 ]; then
        docker-compose up --build --no-start
        [ "$?" != "0" ] && exitScript "Building Docker image failed."
    else
        docker-compose up --build --no-start &>/tmp/phpbenchmarks.docker.build
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker.build && exitScript "Building Docker image failed."
    fi

    if [ $VERBOSE_LEVEL -ge 1 ]; then
        docker-compose up --exit-code-from $containerName
        [ "$?" != "0" ] && exitScript "Benchmark code validation failed."
    else
        docker-compose up --exit-code-from $containerName &>/tmp/phpbenchmarks.docker.benchmark
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker.benchmark && echo "" && exitScript "Benchmark code validation failed."
    fi
}
VALIDATE_CONFIGURATION=true
VALIDATE_CODE=true
VALIDATE_DEV=true
for param in "$@"; do
    if [ "$param" == "--validate-configuration" ]; then
        VALIDATE_CODE=false
    elif [ "$param" == "--validate-code" ]; then
        VALIDATE_CONFIGURATION=false
    elif [ "$param" == "--prod" ]; then
        VALIDATE_DEV=false
    fi
done

if [ ! -d "$INSTALLATION_PATH" ]; then
    exitScript "$INSTALLATION_PATH is not a directory. You have to configure it in docker/.env."
fi
