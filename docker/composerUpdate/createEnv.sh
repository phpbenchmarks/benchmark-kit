#!/usr/bin/env bash

readonly ENV_DIR="$(dirname $0)/docker/composerUpdate"

grep --quiet "INSTALLATION_PATH=$INSTALLATION_PATH" "$ENV_DIR/.env" &>/dev/null
readonly installationPathConfigured=$?

if [ ! -f "$ENV_DIR/.env" ] || [ "$installationPathConfigured" != "0" ]; then
    echoValidationGroupStart "Create $ENV_DIR/.env"
    cp $ENV_DIR/.env.dist $ENV_DIR/.env
    sed -i -e "s~____INSTALLATION_PATH____~$INSTALLATION_PATH~g" $ENV_DIR/.env
    sed -i -e "s~____DOCKER_UID____~$UID~g" $ENV_DIR/.env
    echoValidationGroupEnd
fi
