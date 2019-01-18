#!/usr/bin/env bash

validationGroupName=""

function echoBlock {
    local title=$2
    local titleLength=${#2}

    echo -en "\n\e[$1m\e[1;37m    "
    for x in $(seq 1 $titleLength); do echo -en " "; done;
    echo -en "\e[0m\n"

    echo -en "\e[$1m\e[1;37m  $title  \e[0m\n"
    echo -en "\e[$1m\e[1;37m    "
    for x in $(seq 1 $titleLength); do echo -en " "; done;
    echo -en "\e[0m\n\n"
}

function echoTitle {
    echoBlock 45 "$1"
}

function echoError {
    echo -e "\e[41m ERROR \e[00m \e[31m$1\e[00m"
}

function echoWarning {
    echo -e "\e[43m $1 \e[00m"
}

function echoValidationGroupStart {
    validationGroupName=$1
    if [ $VERBOSE_LEVEL -ge 1 ]; then
        echo -en "\e[44m $1 \e[00m"
        echo ""
    else
        echo "$1 ..."
    fi
}

function echoValidatedTest {
    if [ $VERBOSE_LEVEL -ge 1 ]; then
        echo -e "  \e[44m > \e[00m \e[32mValidated\e[00m $1"
    fi
}

function echoValidationGroupEnd {
    local minVerboseLevel=$1
    local doneSuffix=$2

    if [ "$minVerboseLevel" == "" ]; then
        local minVerboseLevel=1
    fi

    if [ $VERBOSE_LEVEL -ge "$minVerboseLevel" ]; then
        message="Done"
        if [ "$doneSuffix" != "" ]; then
            message="$message $doneSuffix"
        fi
        echo -e "\e[42m $message \e[0m"
        echo ""
    else
        echo -en "\e[1A"
        echo -e "$validationGroupName ... \e[32mdone\e[0m"
    fi
}

function exitScript {
    echo ""
    echoError "$1"
    exit 1
}

function echoValidationOk {
    echoBlock 42 "$1"
}

function definePhpComponentConfigurationValues {
    local phpFile=/var/phpbenchmarks/cli/ComponentConfiguration.php
    sed -i -e "s~____PHPBENCHMARKS_PHP_5_6_ENABLED____~$PHPBENCHMARKS_PHP_5_6_ENABLED~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_PHP_7_0_ENABLED____~$PHPBENCHMARKS_PHP_7_0_ENABLED~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_PHP_7_1_ENABLED____~$PHPBENCHMARKS_PHP_7_1_ENABLED~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_PHP_7_2_ENABLED____~$PHPBENCHMARKS_PHP_7_2_ENABLED~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_PHP_7_3_ENABLED____~$PHPBENCHMARKS_PHP_7_3_ENABLED~g" $phpFile

    sed -i -e "s~____PHPBENCHMARKS_URL____~$PHPBENCHMARKS_URL~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_SLUG____~$PHPBENCHMARKS_SLUG~g" $phpFile

    sed -i -e "s~____PHPBENCHMARKS_MAIN_REPOSITORY____~$PHPBENCHMARKS_MAIN_REPOSITORY~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_VERSION_MAJOR____~$PHPBENCHMARKS_VERSION_MAJOR~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_VERSION_MINOR____~$PHPBENCHMARKS_VERSION_MINOR~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_VERSION_BUGFIX____~$PHPBENCHMARKS_VERSION_BUGFIX~g" $phpFile
}

VERBOSE_LEVEL=0
for param in "$@"; do
    if [ "$param" == "-v" ]; then
        VERBOSE_LEVEL=1
    elif [ "$param" == "-vv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "-vvv" ]; then
        VERBOSE_LEVEL=3
    fi
done
