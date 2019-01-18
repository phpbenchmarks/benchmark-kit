#!/usr/bin/env bash

function echoTitle {
    echo "> $1"
}

function echoValidatedTest {
    if [ $VERBOSE_LEVEL -ge 1 ]; then
        echo -e "\e[32mValidated\e[00m $1"
    fi
}

function echoError {
    echo -e "\e[41m ERROR \e[00m \e[31m$1\e[00m"
}

function echoOk {
    echo -e "\e[42m Ok \e[00m"
}

function exitScript {
    echo ""
    echoError "$1"
    exit 1
}

function copyConfig {
    rm $CONFIG_PATH/*
    cp $INSTALLATION_PATH/.phpbenchmarks/* $CONFIG_PATH
}

function validateConfigFileExists {
    local configFile=$1

    [ ! -f "$CONFIG_PATH/$configFile" ] && exitScript "[$INSTALLATION_PATH/.phpbenchmarks/$configFile] file not found."
    echoValidatedTest "[.phpbenchmarks/$configFile] File exists."
}

function validateCommonConfigExists {
    [ "$PHPBENCHMARKS_PHP_5_6_ENABLED" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_PHP_5_6_ENABLED. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_PHP_5_6_ENABLED ($PHPBENCHMARKS_PHP_5_6_ENABLED)."

    [ "$PHPBENCHMARKS_PHP_7_0_ENABLED" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_PHP_7_0_ENABLED. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_PHP_7_0_ENABLED ($PHPBENCHMARKS_PHP_7_0_ENABLED)."

    [ "$PHPBENCHMARKS_PHP_7_1_ENABLED" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_PHP_7_1_ENABLED. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_PHP_7_1_ENABLED ($PHPBENCHMARKS_PHP_7_1_ENABLED)."

    [ "$PHPBENCHMARKS_PHP_7_2_ENABLED" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_PHP_7_2_ENABLED. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_PHP_7_2_ENABLED ($PHPBENCHMARKS_PHP_7_2_ENABLED)."

    [ "$PHPBENCHMARKS_PHP_7_3_ENABLED" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_PHP_7_3_ENABLED. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_PHP_7_3_ENABLED ($PHPBENCHMARKS_PHP_7_3_ENABLED)."

    [ "$PHPBENCHMARKS_URL" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_URL. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_URL ($PHPBENCHMARKS_URL)."

    [ "$PHPBENCHMARKS_SLUG" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_SLUG. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_SLUG ($PHPBENCHMARKS_SLUG)."

    [ "$PHPBENCHMARKS_MAIN_REPOSITORY" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_MAIN_REPOSITORY. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_MAIN_REPOSITORY ($PHPBENCHMARKS_MAIN_REPOSITORY)."

    [ "$PHPBENCHMARKS_VERSION_MAJOR" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_VERSION_MAJOR. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_VERSION_MAJOR ($PHPBENCHMARKS_VERSION_MAJOR)."

    [ "$PHPBENCHMARKS_VERSION_MINOR" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_VERSION_MINOR. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_VERSION_MINOR ($PHPBENCHMARKS_VERSION_MINOR)."

    [ "$PHPBENCHMARKS_VERSION_BUGFIX" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_VERSION_BUGFIX. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_VERSION_BUGFIX ($PHPBENCHMARKS_VERSION_BUGFIX)."
}

function validateVhost {
    validateVhostVariableExists "____HOST____"
    validateVhostVariableExists "____PROJECT_DIR____"
    validateVhostVariableExists "____PHP_FPM_SOCK____"
}

function validateVhostVariableExists {
    local variable=$1

    grep --quiet "$variable" "$CONFIG_PATH/vhost.conf"
    [ "$?" != "0" ] && exitScript "[$INSTALLATION_PATH/.phpbenchmarks/vhost.conf] Should contains $variable. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/vhost.conf] Contains $variable."
}

function validateCode {
    local containerName=$1

    cd docker

    if [ $VERBOSE_LEVEL -ge 2 ]; then
        docker-compose up --build --no-start
        [ "$?" != "0" ] && exitScript "Building Docker image failed."
    else
        docker-compose up --build --no-start &>/tmp/phpbenchmarks.docker.build
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker.build && exitScript "Building Docker image failed."
    fi

    if [ $VERBOSE_LEVEL -ge 1 ]; then
        docker-compose up --exit-code-from $containerName
        [ "$?" != "0" ] && exitScript "Benchmark code validation failed."
    else
        docker-compose up --exit-code-from $containerName &>/tmp/phpbenchmarks.docker.benchmark
        [ "$?" != "0" ] && cat /tmp/phpbenchmarks.docker.benchmark && echo "" && exitScript "Benchmark code validation failed."
    fi
}

function validateComposerFiles {
    local benchmarkSlug=$1
    local benchmarkId=$2
    local composerJsonPath="$INSTALLATION_PATH/composer.json"

    validateComposerJsonContains \
        "\"name\": \"phpbenchmarks/$PHPBENCHMARKS_SLUG\"" \
        "[composer.json] Project name is valid." \
        "[composer.json] Project name sould be phpbenchmarks/$PHPBENCHMARKS_SLUG."

    validateComposerJsonContains \
        "\"license\": \"proprietary\"" \
        "[composer.json] License is valid." \
        "[composer.json] License should be \"proprietary\"."

    local commonRepository="phpbenchmarks/$PHPBENCHMARKS_SLUG-common"
    if [ $VALIDATE_DEV == true ]; then
        local commonVersion="dev-$PHPBENCHMARKS_SLUG""_""$PHPBENCHMARKS_VERSION_MAJOR""_""$benchmarkSlug""_""dev"
    else
        local commonVersion="$benchmarkId."
    fi
    validateComposerJsonContains \
        "\"$commonRepository\": \"$commonVersion" \
        "[composer.json] Require $commonRepository." \
        "[composer.json] Should require $commonRepository: $commonVersion. See README.md for more informations."

    local mainRepositoryVersion="$PHPBENCHMARKS_VERSION_MAJOR.$PHPBENCHMARKS_VERSION_MINOR.$PHPBENCHMARKS_VERSION_BUGFIX"
    validateComposerJsonContains \
        "\"$PHPBENCHMARKS_MAIN_REPOSITORY\": \"$mainRepositoryVersion\"" \
        "[composer.json] Require $PHPBENCHMARKS_MAIN_REPOSITORY: $mainRepositoryVersion." \
        "Should require $PHPBENCHMARKS_MAIN_REPOSITORY: $mainRepositoryVersion. See README.md for more informations."
}

function validateComposerJsonContains {
    local contains=$1
    local validatedTestMessage=$2
    local invalidTestMessage=$3
    local composerJsonPath="$INSTALLATION_PATH/composer.json"

    grep --quiet "$contains" "$composerJsonPath"
    [ "$?" != "0" ] && exitScript "$invalidTestMessage"
    echoValidatedTest "$validatedTestMessage"
}

VERBOSE_LEVEL=0
VALIDATE_CONFIGURATION=true
VALIDATE_CODE=true
VALIDATE_DEV=true
for param in "$@"; do
    if [ "$param" == "-v" ]; then
        VERBOSE_LEVEL=1
    elif [ "$param" == "-vv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "-vvv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "--validate-configuration" ]; then
        VALIDATE_CODE=false
    elif [ "$param" == "--validate-code" ]; then
        VALIDATE_CONFIGURATION=false
    elif [ "$param" == "--prod" ]; then
        VALIDATE_DEV=false
    fi
done

if [ ! -d "$INSTALLATION_PATH" ]; then
    exitScript "$INSTALLATION_PATH is not a directory. You have to configure it in docker/.env."
fi
