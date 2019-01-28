#!/usr/bin/env bash

# Assume that we are in vendor/bin directory. If you know how to get the real path of this script, don't hesitate ;)
readonly BENCHMARK_KIT_PATH=$(dirname $(cd `dirname $0` && pwd))"/phpbenchmarks/benchmark-kit"
source $BENCHMARK_KIT_PATH/common.sh
source $BENCHMARK_KIT_PATH/validation/configurationValidation.sh

function validateCode {
    local containerName=$1

    cp "$BENCHMARK_KIT_PATH/common.sh" $RESULT_TYPE_PATH/docker/codeValidation

    cd $RESULT_TYPE_PATH/docker/codeValidation

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

    echoValidationGroupStart "Validation of code"
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

echoValidationGroupStart "Validation of .phpbenchmarks directory"
copyConfigurationFiles "$CONFIGURATION_PATH"
assertCommonConfiguration
assertVhostConfiguration
assertInitBenchmark
source "$RESULT_TYPE_PATH/configurationValidation.sh"
echoValidationGroupEnd

source "$RESULT_TYPE_PATH/codeValidation.sh"

echoValidationOk "Code is valid. If everything is done and commited, you can use \"./codeLink.sh $COMPONENT_TYPE $RESULT_TYPE_SLUG $INSTALLATION_PATH\"."
