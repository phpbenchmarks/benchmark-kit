#!/usr/bin/env bash

CONFIGURATION_PATH=$(dirname $0)/.phpbenchmarks

source docker/.env
source ../../common.sh

if [ $VALIDATE_CONFIGURATION == true ]; then
    echoTitle "Validate configuration from $INSTALLATION_PATH"

    copyConfig

    assertConfigFileExists "configuration.sh"
    source ".phpbenchmarks/configuration.sh"
    assertCommonConfiguration

    assertConfigFileExists "vhost.conf"
    validateVhost

    assertConfigFileExists "sudoers"

    assertConfigFileExists "responseBody.txt"

    echoOk
fi

if [ $VALIDATE_CODE == true ]; then
    echoTitle "Validate code"
    validateCode "phpbenchmarks_framework_hello_world"
    echoOk
fi
