#!/usr/bin/env bash

function echoTitle {
    echo "> $1"
}

function echoError {
    echo -e "\e[41m ERROR \e[00m \e[31m$1\e[00m"
}

function echoOk {
    echo -e "\e[42m Ok \e[00m"
}

function exitScript {
    echo ""
    echoError "$1"
    exit 1
}

function copyConfig {
    local installationPath=$1
    local configPath=$2

    rm $configPath/*
    cp $installationPath/.phpbenchmarks/* $configPath
}

function validateConfigFileExists {
    local installationPath=$1
    local configPath=$2
    local configFile=$3

    [ ! -f "$configPath/$configFile" ] && exitScript "$installationPath/.phpbenchmarks/$configFile file not found."
}

function validateVhost {
    local installationPath=$1
    local configPath=$2

    validateConfigFileExists "$installationPath" "$configPath" "vhost.conf"

    validateVhostVariableExists "$installationPath" "$configPath" "____HOST____"
    validateVhostVariableExists "$installationPath" "$configPath" "____PROJECT_DIR____"
    validateVhostVariableExists "$installationPath" "$configPath" "____PHP_FPM_SOCK____"
}

function validateVhostVariableExists {
    local installationPath=$1
    local configPath=$2
    local variable=$3

    grep --quiet "$variable" "$configPath/vhost.conf"
    [ "$?" != "0" ] && exitScript "$installationPath/.phpbenchmarks/vhost.conf should contains $variable. See README.md for more informations."
}

function validateCode {
    local containerName=$1

    cd docker

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

VERBOSE_LEVEL=0
VALIDATE_CONFIGURATION=true
VALIDATE_CODE=true
for param in "$@"; do
    if [ "$param" == "-v" ]; then
        VERBOSE_LEVEL=1
    elif [ "$param" == "-vv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "-vvv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "--validate-configuration" ]; then
        VALIDATE_CODE=false
elif [ "$param" == "--validate-code" ]; then
        VALIDATE_CONFIGURATION=false
    fi
done
