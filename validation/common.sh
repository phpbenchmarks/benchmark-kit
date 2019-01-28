#!/usr/bin/env bash

COMPONENT_TYPE=$1
RESULT_TYPE_SLUG=$2
showCliWarning=false

if [ "$COMPONENT_TYPE" != "framework" ] && [ "$COMPONENT_TYPE" != "templateEngine" ]; then
    showCliWarning=true
    echoAsk "Choose component type" false
    echo ""
    echo "   framework"
    echo "   templateEngine"
    read COMPONENT_TYPE
fi
if [ "$COMPONENT_TYPE" != "framework" ] && [ "$COMPONENT_TYPE" != "templateEngine" ]; then
    exitScript "Invalid component type."
fi

if [ "$RESULT_TYPE_SLUG" != "hello-world" ] && [ "$RESULT_TYPE_SLUG" != "rest-api" ]; then
    showCliWarning=true
    echoAsk "Choose benchmark type" false
    echo ""
    echo "   hello-world"
    echo "   rest-api"
    read RESULT_TYPE_SLUG
fi
if [ "$RESULT_TYPE_SLUG" != "hello-world" ] && [ "$RESULT_TYPE_SLUG" != "rest-api" ]; then
    exitScript "Invalid benchmark type."
fi

if [ $showCliWarning == true ]; then
    echoWarning "You could use \"$0 $COMPONENT_TYPE $RESULT_TYPE_SLUG\"."
fi

readonly INSTALLATION_PATH="$BENCHMARK_KIT_PATH/../../.."
readonly RESULT_TYPE_PATH="$BENCHMARK_KIT_PATH/validation/$COMPONENT_TYPE/$RESULT_TYPE_SLUG"
if [ ! -d $RESULT_TYPE_PATH ]; then
    exitScript "$RESULT_TYPE_PATH is not a valid directory."
fi
readonly CONFIGURATION_PATH="$RESULT_TYPE_PATH/componentFiles/.phpbenchmarks"
if [ ! -d "$CONFIGURATION_PATH" ]; then
    mkdir "$CONFIGURATION_PATH"
fi
