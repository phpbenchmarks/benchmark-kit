#!/usr/bin/env bash

source /var/phpbenchmarks/configuration.sh

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

function createVhosts {
    local php56VhostFile=/etc/nginx/sites-enabled/benchmark.php56.conf
    cp /var/phpbenchmarks/vhost.conf $php56VhostFile
    sed -i -e "s/____HOST____/php56.benchmark.loc/g" $php56VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php56VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php5.6-fpm.sock/g" $php56VhostFile

    local php70VhostFile=/etc/nginx/sites-enabled/benchmark.php70.conf
    cp /var/phpbenchmarks/vhost.conf $php70VhostFile
    sed -i -e "s/____HOST____/php70.benchmark.loc/g" $php70VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php70VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.0-fpm.sock/g" $php70VhostFile

    local php71VhostFile=/etc/nginx/sites-enabled/benchmark.php71.conf
    cp /var/phpbenchmarks/vhost.conf $php71VhostFile
    sed -i -e "s/____HOST____/php71.benchmark.loc/g" $php71VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php71VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.1-fpm.sock/g" $php71VhostFile

    local php72VhostFile=/etc/nginx/sites-enabled/benchmark.php72.conf
    cp /var/phpbenchmarks/vhost.conf $php72VhostFile
    sed -i -e "s/____HOST____/php72.benchmark.loc/g" $php72VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php72VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.2-fpm.sock/g" $php72VhostFile

    local php73VhostFile=/etc/nginx/sites-enabled/benchmark.php73.conf
    cp /var/phpbenchmarks/vhost.conf $php73VhostFile
    sed -i -e "s/____HOST____/php73.benchmark.loc/g" $php73VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php73VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.3-fpm.sock/g" $php73VhostFile
}

function startNginx {
    sudo /usr/sbin/service nginx start 1>/dev/null
    [ $? != "0" ] && validationFailedExit "Error while starting nginx."
}

function validateBody {
    local phpVersion=$1
    local benchmarkUrl="http://php${phpVersion//.}.benchmark.loc$2"
    local bodyFile="/tmp/benchmark.body"
    local responseFile="/var/phpbenchmarks/responseBody.txt"

    echoTitle "Testing $benchmarkUrl"

    sudo /usr/sbin/service php$phpVersion-fpm start
    [ "$?" != "0" ] && validationFailedExit "Could not start PHP $phpVersion FPM"

    cd /var/www/phpbenchmarks
    [ "$?" != "0" ] && validationFailedExit "Could not change directory to /var/www/phpbenchmarks"

    cp "composer.lock.php$phpVersion" composer.lock
    [ "$?" != "0" ] && validationFailedExit "Could not copy composer.lock.php$phpVersion to composer.lock"

    source init_benchmark.sh
    [ "$?" != "0" ] && validationFailedExit "File init_benchmark.sh not found."
    init &>/dev/null
    [ "$?" != "0" ] && validationFailedExit "Could not call init_benchmark.sh::init()."

    if [ -f $bodyFile ]; then
        rm $bodyFile
    fi

    local httpCode=$(wget --server-response -O $bodyFile $benchmarkUrl 2>&1 | awk '/^  HTTP/{print $2}')

    if [ "$httpCode" == "200" ]; then
        echoValidatedTest "HTTP code is 200."

        if [ "$(cat $bodyFile)" == "$(cat $responseFile)" ]; then
            echoValidatedTest "Body is valid."
        else
            validationFailed "Body is invalid."
            echo "Http code:"
            echo $httpCode
            echo "Expected body:"
            cat $responseFile
            echo ""
            echo "Body:"
            cat $bodyFile
            exit 1
        fi
    else
        validationFailedExit "HTTP code should be 200, but is $httpCode."
    fi

    cd - &>/dev/null
}

createVhosts
startNginx

[ "$PHPBENCHMARKS_PHP_5_6_ENABLED" == "true" ] && validateBody "5.6" "$PHPBENCHMARKS_URL"
[ "$PHPBENCHMARKS_PHP_7_0_ENABLED" == "true" ] && validateBody "7.0" "$PHPBENCHMARKS_URL"
[ "$PHPBENCHMARKS_PHP_7_1_ENABLED" == "true" ] && validateBody "7.1" "$PHPBENCHMARKS_URL"
[ "$PHPBENCHMARKS_PHP_7_2_ENABLED" == "true" ] && validateBody "7.2" "$PHPBENCHMARKS_URL"
[ "$PHPBENCHMARKS_PHP_7_3_ENABLED" == "true" ] && validateBody "7.3" "$PHPBENCHMARKS_URL"
