#!/usr/bin/env bash

COMPONENT_TYPE=$1
RESULT_TYPE_SLUG=$2
INSTALLATION_PATH=$3
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

if [ "$INSTALLATION_PATH" == "" ]; then
    showCliWarning=true
    echoAsk "Path to your code?" false
    echo ""
    read INSTALLATION_PATH
fi
if [ ! -d "$INSTALLATION_PATH" ]; then
    exitScript "Invalid path to your code: $INSTALLATION_PATH."
fi

if [ $showCliWarning == true ]; then
    echoWarning "You could use \"$0 $COMPONENT_TYPE $RESULT_TYPE_SLUG $INSTALLATION_PATH\"."
fi

readonly RESULT_TYPE_PATH="$(dirname $0)/validation/$COMPONENT_TYPE/$RESULT_TYPE_SLUG"
readonly CONFIGURATION_PATH="$RESULT_TYPE_PATH/componentFiles/.phpbenchmarks"
