#!/usr/bin/env bash

COMPONENT_TYPE=$1
RESULT_TYPE=$2
INSTALLATION_PATH=$3
showCliWarning=false

if [ "$COMPONENT_TYPE" != "framework" ] && [ "$COMPONENT_TYPE" != "templateEngine" ]; then
    showCliWarning=true
    echo "> Choose component type:"
    echo "   framework"
    echo "   templateEngine"
    read COMPONENT_TYPE
fi
if [ "$COMPONENT_TYPE" != "framework" ] && [ "$COMPONENT_TYPE" != "templateEngine" ]; then
    exitScript "Invalid component type."
fi

if [ "$RESULT_TYPE" != "helloWorld" ] && [ "$RESULT_TYPE" != "restApi" ]; then
    showCliWarning=true
    echo "> Choose benchmark type:"
    echo "   helloWorld"
    echo "   restApi"
    read RESULT_TYPE
fi
if [ "$RESULT_TYPE" != "helloWorld" ] && [ "$RESULT_TYPE" != "restApi" ]; then
    exitScript "Invalid benchmark type."
fi

if [ "$INSTALLATION_PATH" == "" ]; then
    showCliWarning=true
    echo "> Path to your code?"
    read INSTALLATION_PATH
fi
if [ ! -d "$INSTALLATION_PATH" ]; then
    exitScript "Invalid path to your code: $INSTALLATION_PATH."
fi

if [ $showCliWarning == true ]; then
    echoWarning "You could use \"$0 $COMPONENT_TYPE $RESULT_TYPE $INSTALLATION_PATH\"."
fi

readonly RESULT_TYPE_PATH="$(dirname $0)/validation/$COMPONENT_TYPE/$RESULT_TYPE"
readonly CONFIGURATION_PATH="$RESULT_TYPE_PATH/componentFiles/.phpbenchmarks"
