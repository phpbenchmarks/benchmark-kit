#!/usr/bin/env bash

source "$(dirname $0)/validation/common.sh"

function copyConfigurationFiles {
    rm $CONFIGURATION_PATH/*
    cp $INSTALLATION_PATH/.phpbenchmarks/* $CONFIGURATION_PATH
}

function assertConfigurationFileExist {
    local configFile=$1

    [ ! -f "$CONFIGURATION_PATH/$configFile" ] \
        && exitScript "[$INSTALLATION_PATH/.phpbenchmarks/$configFile] File not found. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/$configFile] The file exist."
}

function assertFileExist {
    local file=$1

    [ ! -f "$INSTALLATION_PATH/$file" ] \
        && exitScript "[$INSTALLATION_PATH/$file] File not found. See README.md for more informations."
    echoValidatedTest "[$file] The file exist."
}

function assertCommonConfiguration {
    assertConfigurationFileExist "configuration.sh"
    source "$CONFIGURATION_PATH/configuration.sh"

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

function assertVhostConfiguration {
    assertConfigurationFileExist "vhost.conf"
    assertVhostVariableExist "____HOST____"
    assertVhostVariableExist "____PROJECT_DIR____"
    assertVhostVariableExist "____PHP_FPM_SOCK____"
}

function assertVhostVariableExist {
    local variable=$1

    grep --quiet "$variable" "$CONFIGURATION_PATH/vhost.conf"
    [ "$?" != "0" ] && exitScript "[.phpbenchmarks/vhost.conf] Should contains $variable. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/vhost.conf] Contains $variable."
}

function assertInitBenchmark {
    assertConfigurationFileExist "initBenchmark.sh"

    source $CONFIGURATION_PATH/initBenchmark.sh
    [ "$?" != "0" ] && validationFailedExit "File init_benchmark.sh could not be included."

    type initBenchmark &>/dev/null
    [ "$?" != "0" ] \
        && cat /tmp/phpbenchmarks.docker.build \
        && validationFailedExit "Function init_benchmark.sh::initBenchmark() does not exist. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/initBenchmark.sh] Function initBenchmark() exit."
}

function assertReadMe {
    echoValidationGroupStart "Validation of README.md"
    assertFileExist "README.md"
    echoValidationGroupEnd
}
