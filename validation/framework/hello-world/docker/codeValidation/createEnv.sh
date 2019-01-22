#!/usr/bin/env bash

grep --quiet "INSTALLATION_PATH=$INSTALLATION_PATH" ".env" &>/dev/null
readonly installationPathConfigured=$?

if [ ! -f ".env" ] || [ "$installationPathConfigured" != "0" ]; then
    echoValidationGroupStart "Create .env for Docker container"

    cp .env.dist .env
    [ $? != "0" ] && exitScript
    echoValidatedTest "File created."

    sed -i -e "s~____INSTALLATION_PATH____~$INSTALLATION_PATH~g" .env
    [ $? != "0" ] && exitScript
    echoValidatedTest "\$INSTALLATION_PATH defined to $INSTALLATION_PATH."

    sed -i -e "s~____DOCKER_UID____~$UID~g" .env
    [ $? != "0" ] && exitScript
    echoValidatedTest "\$DOCKER_UID defined to $UID."

    echoValidationGroupEnd
fi
