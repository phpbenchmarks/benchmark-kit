#!/usr/bin/env bash

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

function echoValidatedTest {
    echo -e "\e[32mValidated\e[00m $1"
}

function validationFailed {
    echoBlock 41 "$1"
}

function validationFailedExit {
    validationFailed "$1"
    exit 1
}

function validateBody {
    local phpVersion=$1
    local hostPrefix=${phpVersion//.}
    local bodyFile="/tmp/benchmark.body"
    local responseFile="/var/benchmark/response.txt"

    echoTitle "Testing code with PHP $phpVersion"

    sudo /usr/sbin/service php$phpVersion-fpm start
    [ "$?" != "0" ] && validationFailedExit "Could not start PHP $phpVersion FPM"

    cd /var/www/phpbenchmarks
    [ "$?" != "0" ] && validationFailedExit "Could not change directory to /var/www/phpbenchmarks"

    cp "composer.lock.php$phpVersion" composer.lock
    [ "$?" != "0" ] && validationFailedExit "Could not copy composer.lock.php$phpVersion to composer.lock"

    source init_benchmark.sh && init
    [ "$?" != "0" ] && validationFailedExit "Could not call init_benchmark.sh::init()"

    if [ -f $bodyFile ]; then
        rm $bodyFile
    fi

    wget -O $bodyFile http://$hostPrefix.benchmark.loc/benchmark/helloworld &>/dev/null
    if [ "$(cat $bodyFile)" == "$(cat $responseFile)" ]; then
        echoValidatedTest "Body with PHP $phpVersion."
    else
        validationFailed "Body with PHP $phpVersion is invalid"
        echo "Expected body:"
        cat $responseFile
        echo ""
        echo "Body:"
        cat $bodyFile
        exit 1
    fi

    cd - &>/dev/null
}

PHP_5_6_ENABLED=true
PHP_7_0_ENABLED=true
PHP_7_1_ENABLED=true
PHP_7_2_ENABLED=true
PHP_7_3_ENABLED=true
source /var/benchmark/configuration.sh

sudo /usr/sbin/service nginx start 1>/dev/null

[ "$PHP_5_6_ENABLED" == "true" ] && validateBody "5.6"
[ "$PHP_7_0_ENABLED" == "true" ] && validateBody "7.0"
[ "$PHP_7_1_ENABLED" == "true" ] && validateBody "7.1"
[ "$PHP_7_2_ENABLED" == "true" ] && validateBody "7.2"
[ "$PHP_7_3_ENABLED" == "true" ] && validateBody "7.3"
