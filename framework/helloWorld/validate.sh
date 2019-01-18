#!/usr/bin/env bash

CONFIG_PATH=$(dirname $0)/.phpbenchmarks

source docker/.env
source ../../common.sh

if [ $VALIDATE_CONFIGURATION == true ]; then
    echoTitle "Validate configuration from $INSTALLATION_PATH"

    copyConfig

    validateConfigFileExists "configuration.sh"
    source ".phpbenchmarks/configuration.sh"
    validateCommonConfigExists

    validateConfigFileExists "vhost.conf"
    validateVhost

    validateConfigFileExists "sudoers"

    validateConfigFileExists "responseBody.txt"

    validateComposerFiles "hello-world"

    echoOk
fi

if [ $VALIDATE_CODE == true ]; then
    echoTitle "Validate code"
    validateCode "phpbenchmarks_framework_hello_world"
    echoOk
fi
