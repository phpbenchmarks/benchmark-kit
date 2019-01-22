#!/usr/bin/env bash

readonly ENV_DIR="$(dirname $0)/docker/composerUpdate"

echoValidationGroupStart "Create $ENV_DIR/.env"

cp $ENV_DIR/.env.dist $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "File created from .env.dist."

sed -i -e "s~____INSTALLATION_PATH____~$INSTALLATION_PATH~g" $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "\$INSTALLATION_PATH defined to $INSTALLATION_PATH."

sed -i -e "s~____DOCKER_UID____~$UID~g" $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "\$DOCKER_UID defined to $UID."

sed -i -e "s~____VALIDATE_DEV____~$VALIDATE_DEV~g" $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "\$VALIDATE_DEV defined to $VALIDATE_DEV."

sed -i -e "s~____REPOSITORIES_CREATED____~$REPOSITORIES_CREATED~g" $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "\$REPOSITORIES_CREATED defined to $REPOSITORIES_CREATED."

sed -i -e "s~____RESULT_TYPE_SLUG____~$RESULT_TYPE_SLUG~g" $ENV_DIR/.env
[ $? != "0" ] && exitScript
echoValidatedTest "\$RESULT_TYPE_SLUG defined to $RESULT_TYPE_SLUG."

echoValidationGroupEnd
