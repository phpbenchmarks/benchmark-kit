#!/usr/bin/env bash

source /var/phpbenchmarks/componentFiles/.phpbenchmarks/configuration.sh
source /var/phpbenchmarks/common.sh

function createVhosts {
    local php56VhostFile=/etc/nginx/sites-enabled/benchmark.php56.conf
    cp $DOCKER_CONFIGURATION_PATH/vhost.conf $php56VhostFile
    sed -i -e "s/____HOST____/php56.benchmark.loc/g" $php56VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php56VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php5.6-fpm.sock/g" $php56VhostFile

    local php70VhostFile=/etc/nginx/sites-enabled/benchmark.php70.conf
    cp $DOCKER_CONFIGURATION_PATH/vhost.conf $php70VhostFile
    sed -i -e "s/____HOST____/php70.benchmark.loc/g" $php70VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php70VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.0-fpm.sock/g" $php70VhostFile

    local php71VhostFile=/etc/nginx/sites-enabled/benchmark.php71.conf
    cp $DOCKER_CONFIGURATION_PATH/vhost.conf $php71VhostFile
    sed -i -e "s/____HOST____/php71.benchmark.loc/g" $php71VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php71VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.1-fpm.sock/g" $php71VhostFile

    local php72VhostFile=/etc/nginx/sites-enabled/benchmark.php72.conf
    cp $DOCKER_CONFIGURATION_PATH/vhost.conf $php72VhostFile
    sed -i -e "s/____HOST____/php72.benchmark.loc/g" $php72VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php72VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.2-fpm.sock/g" $php72VhostFile

    local php73VhostFile=/etc/nginx/sites-enabled/benchmark.php73.conf
    cp $DOCKER_CONFIGURATION_PATH/vhost.conf $php73VhostFile
    sed -i -e "s/____HOST____/php73.benchmark.loc/g" $php73VhostFile
    sed -i -e "s~____PROJECT_DIR____~/var/www/phpbenchmarks~g" $php73VhostFile
    sed -i -e "s/____PHP_FPM_SOCK____/php7.3-fpm.sock/g" $php73VhostFile
}

function startNginx {
    sudo /usr/sbin/service nginx start &>/tmp/nginx.start
    [ $? != "0" ] && cat /tmp/nginx.start && exitScript "Error while starting nginx."
}

function validateBenchmarkUrlBodies {
    [ "$PHPBENCHMARKS_PHP_5_6_ENABLED" == "true" ] && validateBody "5.6" "$PHPBENCHMARKS_BENCHMARK_URL"
    [ "$PHPBENCHMARKS_PHP_7_0_ENABLED" == "true" ] && validateBody "7.0" "$PHPBENCHMARKS_BENCHMARK_URL"
    [ "$PHPBENCHMARKS_PHP_7_1_ENABLED" == "true" ] && validateBody "7.1" "$PHPBENCHMARKS_BENCHMARK_URL"
    [ "$PHPBENCHMARKS_PHP_7_2_ENABLED" == "true" ] && validateBody "7.2" "$PHPBENCHMARKS_BENCHMARK_URL"
    [ "$PHPBENCHMARKS_PHP_7_3_ENABLED" == "true" ] && validateBody "7.3" "$PHPBENCHMARKS_BENCHMARK_URL"
}

function callInitBenchmark {
    echoValidationGroupStart "Call .phpbenchmarks/initBenchmark.sh::initBenchmark()"

    cd /var/www/phpbenchmarks

    source .phpbenchmarks/initBenchmark.sh
    [ "$?" != "0" ] && exitScript "File .phpbenchmarks/initBenchmark.sh not found."

    initBenchmark
    [ "$?" != "0" ] && exitScript "Could not call .phpbenchmarks/initBenchmark.sh::initBenchmark()."

    cd - &>/dev/null
    echoValidationGroupEnd
}


function validateBody {
    local phpVersion=$1
    local benchmarkUrl="http://php${phpVersion//.}.benchmark.loc$2"
    local bodyFile="/tmp/benchmark.body"
    local responseFileDir="$DOCKER_CONFIGURATION_PATH/responseBody"

    echoValidationGroupStart "Validating $benchmarkUrl"

    sudo /usr/sbin/service php$phpVersion-fpm start
    [ "$?" != "0" ] && exitScript "Could not start PHP $phpVersion FPM."

    cd /var/www/phpbenchmarks
    [ "$?" != "0" ] && exitScript "Could not change directory to /var/www/phpbenchmarks."

    cp "composer.lock.php$phpVersion" composer.lock
    [ "$?" != "0" ] && exitScript "Could not copy composer.lock.php$phpVersion to composer.lock."

    if [ -f $bodyFile ]; then
        rm $bodyFile
    fi

    local httpCode=$(wget --server-response -O $bodyFile $benchmarkUrl 2>&1 | awk '/^  HTTP/{print $2}')

    if [ "$httpCode" == "200" ]; then
        echoValidatedTest "HTTP code is 200."

        local oldIFS=$IFS
        IFS=
        local bodyContent=$(cat $bodyFile)
        local contentValidated=false
        for responseFile in $responseFileDir/*; do
            local expectedContent=$(cat $responseFile)
            if [ "$bodyContent" == "$expectedContent" ]; then
                echoValidatedTest "Body is equal to .phpbenchmarks/responseBody/$(basename $responseFile) content."
                contentValidated=true
            fi
        done
        IFS=$oldIFS

        if [ $contentValidated == false ]; then
            echoError "Body should be equal to the content of a file in .phpbenchmarks/responseBody."
            cat $bodyFile
            echo ""
            exit 1
        fi
    else
        exitScript "HTTP code should be 200, but is $httpCode."
    fi

    echoValidationGroupEnd

    cd - &>/dev/null
}

createVhosts
startNginx
definePhpComponentConfigurationValues
