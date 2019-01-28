#!/usr/bin/env bash

source "$BENCHMARK_KIT_PATH/validation/common.sh"

function copyConfigurationFiles {
    local destinationPath=$1

    if [ -d "$destinationPath" ]; then
        rm -rf $destinationPath
    fi
    mkdir $destinationPath
    cp -R $INSTALLATION_PATH/.phpbenchmarks/* $destinationPath/
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

    [ "$PHPBENCHMARKS_COMPONENT_NAME" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_COMPONENT_NAME. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_COMPONENT_NAME ($PHPBENCHMARKS_COMPONENT_NAME)."

    [ "$PHPBENCHMARKS_COMPONENT_SLUG" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_COMPONENT_SLUG. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_COMPONENT_SLUG ($PHPBENCHMARKS_COMPONENT_SLUG)."

    [ "$PHPBENCHMARKS_BENCHMARK_URL" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_BENCHMARK_URL. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_BENCHMARK_URL ($PHPBENCHMARKS_BENCHMARK_URL)."

    [ "$PHPBENCHMARKS_DEPENDENCY_NAME" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_DEPENDENCY_NAME. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_DEPENDENCY_NAME ($PHPBENCHMARKS_DEPENDENCY_NAME)."

    [ "$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION ($PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION)."

    [ "$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION ($PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION)."

    [ "$PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION ($PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION)."
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
    [ "$?" != "0" ] && exitScript "File .phpbenchmarks/initBenchmark.sh could not be included."

    type initBenchmark &>/dev/null
    [ "$?" != "0" ] && exitScript "Function .phpbenchmarks/initBenchmark.sh::initBenchmark() does not exist. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/initBenchmark.sh] Function initBenchmark() exist."
}
