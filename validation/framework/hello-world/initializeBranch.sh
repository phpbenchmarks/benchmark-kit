#!/usr/bin/env bash

if [ ! -f "$INSTALLATION_PATH/.phpbenchmarks/responseBody/responseBody.txt" ]; then
    echoValidationGroupStart "Create .phpbenchmarks/responseBody/responseBody.txt"
    echo "Hello World !" > "$INSTALLATION_PATH/.phpbenchmarks/responseBody/responseBody.txt"
    [ $? != "0" ] && exitScript "Error while creating $INSTALLATION_PATH/.phpbenchmarks/responseBody/responseBody.txt."
    echoValidatedTest "File created."
    echoValidationGroupEnd
fi
