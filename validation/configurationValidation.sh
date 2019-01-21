#!/usr/bin/env bash

source "$(dirname $0)/validation/common.sh"

function copyReadMe {
    local readMePath="$CONFIGURATION_PATH/../README.md"
    if [ -f "$readMePath" ]; then
        rm "$readMePath"
    fi

    cp $INSTALLATION_PATH/README.md $readMePath
    [ $? != "0" ] && exitScript "[README.md] Error while copying README.md."
}

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

    [ "$PHPBENCHMARKS_NAME" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_NAME. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_NAME ($PHPBENCHMARKS_NAME)."

    [ "$PHPBENCHMARKS_SLUG" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_SLUG. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_SLUG ($PHPBENCHMARKS_SLUG)."

    [ "$PHPBENCHMARKS_BENCHMARK_URL" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_BENCHMARK_URL. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_BENCHMARK_URL ($PHPBENCHMARKS_BENCHMARK_URL)."

    [ "$PHPBENCHMARKS_MAIN_REPOSITORY" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_MAIN_REPOSITORY. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_MAIN_REPOSITORY ($PHPBENCHMARKS_MAIN_REPOSITORY)."

    [ "$PHPBENCHMARKS_MAJOR_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_MAJOR_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_MAJOR_VERSION ($PHPBENCHMARKS_MAJOR_VERSION)."

    [ "$PHPBENCHMARKS_MINOR_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_MINOR_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_MINOR_VERSION ($PHPBENCHMARKS_MINOR_VERSION)."

    [ "$PHPBENCHMARKS_BUGFIX_VERSION" == "" ] \
        && exitScript "[.phpbenchmarks/configuration.sh] Should define \$PHPBENCHMARKS_BUGFIX_VERSION. See README.md for more informations."
    echoValidatedTest "[.phpbenchmarks/configuration.sh] Define \$PHPBENCHMARKS_BUGFIX_VERSION ($PHPBENCHMARKS_BUGFIX_VERSION)."
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

    local oldIFS=$IFS
    IFS=
    local validReadMeContent=$(cat validation/mainRepositoryReadme.md)
    validReadMeContent=${validReadMeContent//____PHPBENCHMARKS_SLUG____/$PHPBENCHMARKS_SLUG}
    validReadMeContent=${validReadMeContent//____PHPBENCHMARKS_NAME____/$PHPBENCHMARKS_NAME}
    validReadMeContent=${validReadMeContent//____PHPBENCHMARKS_MAJOR_VERSION____/$PHPBENCHMARKS_MAJOR_VERSION}
    validReadMeContent=${validReadMeContent//____PHPBENCHMARKS_MINOR_VERSION____/$PHPBENCHMARKS_MINOR_VERSION}
    local readMeContent=$(cat $CONFIGURATION_PATH/../README.md)
    IFS=$oldIFS
    if [ "$validReadMeContent" != "$readMeContent" ]; then
        echoWarningAsk "Content of README.md is not valid. Do you want to modify it automaticaly? [Y/n]"
        read editReadMe

        if [ $VERBOSE_LEVEL -eq 0 ]; then
            echo ""
        fi

        if [ "$editReadMe" == "" ] || [ "$editReadMe" == "y" ] || [ "$editReadMe" == "Y" ]; then
            echo "$validReadMeContent" > $INSTALLATION_PATH/README.md

            copyReadMe
            local readMeContent=$(cat $CONFIGURATION_PATH/../README.md)
        fi
    fi
    [ "$validReadMeContent" != "$readMeContent" ] && exitScript "[README.md] Content is invalid."
    echoValidatedTest "[README.md] Content is valid."

    echoValidationGroupEnd
}
