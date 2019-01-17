#!/usr/bin/env bash

INSTALLATION_PATH=/dev/null
VALIDATE_CONFIGURATION=true
VALIDATE_CODE=true

source docker/.env
source ../../common.sh

CONFIG_PATH=$(dirname $0)/docker/.phpbenchmarks

if [ $VALIDATE_CONFIGURATION == true ]; then
    echoTitle "Validate configuration from $INSTALLATION_PATH"

    copyConfig "$INSTALLATION_PATH" "$CONFIG_PATH"

    validateConfigFileExists "$INSTALLATION_PATH" "$CONFIG_PATH" "sudoers"
    validateConfigFileExists "$INSTALLATION_PATH" "$CONFIG_PATH" "response.txt"

    validateVhost "$INSTALLATION_PATH" "$CONFIG_PATH"

    echoOk
fi

if [ $VALIDATE_CODE == true ]; then
    echoTitle "Validate code"
    validateCode "phpbenchmarks_framework_hello_world"
    echoOk
fi
