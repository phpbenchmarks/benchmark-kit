#!/usr/bin/env bash

function validateCode {
    local containerName=$1

    cp common.sh $RESULT_TYPE_PATH/docker/validate

    cd $RESULT_TYPE_PATH/docker/validate

    source createEnv.sh

    echoValidationGroupStart "Building Docker container"
    if [ $VERBOSE_LEVEL -ge 2 ]; then
        docker-compose up --build --no-start
        [ "$?" != "0" ] && exitScript "Building Docker image failed."
    else
        docker-compose up --build --no-start &>/tmp/phpbenchmarks.docker
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker && exitScript "Building Docker image failed."
        rm /tmp/phpbenchmarks.docker
    fi
    echoValidationGroupEnd

    echoValidationGroupStart "Validating code"
    if [ $VERBOSE_LEVEL -ge 1 ]; then
        docker-compose up --abort-on-container-exit --exit-code-from $containerName
        [ "$?" != "0" ] && exitScript "Benchmark code validation failed."
    else
        docker-compose up --abort-on-container-exit --exit-code-from $containerName &>/tmp/phpbenchmarks.docker
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker && echo "" && exitScript "Benchmark code validation failed."
        rm /tmp/phpbenchmarks.docker
    fi
    echoValidationGroupEnd
}

source common.sh
source validation/configurationValidation.sh

echoValidationGroupStart "Validation of .phpbenchmarks directory"
copyConfigurationFiles
assertCommonConfiguration
assertVhostConfiguration
assertInitBenchmark
source "$RESULT_TYPE_PATH/configurationValidation.sh"
echoValidationGroupEnd

source "$RESULT_TYPE_PATH/validate.sh"
