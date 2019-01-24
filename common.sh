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

function echoError {
    if [ $VERBOSE_LEVEL == 0 ]; then
        echo -e "\e[41m ERROR \e[00m \e[31m$1\e[00m"
    else
        echo -e "  \e[41m > \e[00m \e[41m ERROR \e[00m \e[31m$1\e[00m"
    fi
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

function echoValidationWarning {
    if [ $VERBOSE_LEVEL -ge 1 ]; then
        echo -e "  \e[43m > \e[00m \e[43m $1 \e[00m"
    else
        echo -e "\e[43m $1 \e[00m"
    fi
}

function echoValidatedTest {
    local message=$1
    local title=$2
    [ "$title" == "" ] && title="Validated"

    if [ $VERBOSE_LEVEL -ge 1 ]; then
        echo -e "  \e[42m > \e[00m \e[32m$title\e[00m $1"
    fi
}

function echoAsk {
    local message=$1
    local isInValidationGroup=$2

    if [ "$isInValidationGroup" == "false" ] || [ $VERBOSE_LEVEL -le 0 ]; then
        echo -n -e "\e[45m $message \e[00m "
    else
        echo -n -e "  \e[45m > \e[00m \e[45m $message \e[00m "
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
        echo -e "  \e[42m $message \e[0m"
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

    sed -i -e "s~____PHPBENCHMARKS_BENCHMARK_URL____~$PHPBENCHMARKS_BENCHMARK_URL~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_SLUG____~$PHPBENCHMARKS_SLUG~g" $phpFile

    sed -i -e "s~____PHPBENCHMARKS_DEPENDENCY_NAME____~$PHPBENCHMARKS_DEPENDENCY_NAME~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION____~$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION____~$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION~g" $phpFile
    sed -i -e "s~____PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION____~$PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION~g" $phpFile
}

function validateComposerJson {
    cd /var/phpbenchmarks/cli
    echoValidationGroupStart "Validation of composer.json"
    php console phpbenchmarks:validate:composerjson $VALIDATE_DEV $REPOSITORIES_CREATED $RESULT_TYPE_SLUG
    [ "$?" != "0" ] && exit 1
    echoValidationGroupEnd
    cd - &>/dev/null
}

function validateComposerLock {
    cd /var/phpbenchmarks/cli
    echoValidationGroupStart "Validation of composer.lock.phpX.Y"
    php console phpbenchmarks:validate:composerlock $VALIDATE_DEV $REPOSITORIES_CREATED $RESULT_TYPE_SLUG
    [ "$?" != "0" ] && exit 1
    echoValidationGroupEnd
    cd - &>/dev/null
}

function validateBranchName {
    echoValidationGroupStart "Validation of git branch"

    if [ $REPOSITORIES_CREATED == false ]; then
        echoValidationWarning "Branch names are not validated. Don't forget to remove '--repositories-not-created' parameter when repositories will be created."
    else
        local gitBranch=$(cd $INSTALLATION_PATH && git branch --no-color 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/(\1)/' -e 's/(//g' -e 's/)//g')
        local expectedGitBranch="$PHPBENCHMARKS_SLUG"_"$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION.$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION"_"$RESULT_TYPE_SLUG"
        if [ $VALIDATE_DEV == true ]; then
            expectedGitBranch=$expectedGitBranch"_prepare"
        fi

        [ "$gitBranch" != "$expectedGitBranch" ] && echoError "Git branch should be $expectedGitBranch, but is $gitBranch." && exit 1
        echoValidatedTest "Git branch is $expectedGitBranch."
    fi

    echoValidationGroupEnd
}

function definePhpCliVersion {
    local phpVersion=$1

    sudo /usr/bin/update-alternatives --set php /usr/bin/php$phpVersion
}

VERBOSE_LEVEL=0
for param in "$@"; do
    if [ "$param" == "-v" ]; then
        VERBOSE_LEVEL=1
    elif [ "$param" == "-vv" ]; then
        VERBOSE_LEVEL=2
    elif [ "$param" == "-vvv" ]; then
        VERBOSE_LEVEL=3
    elif [ "$param" == "--prod" ]; then
        VALIDATE_DEV=false
    elif [ "$param" == "--repositories-not-created" ]; then
        REPOSITORIES_CREATED=false
    fi
done

if [ "$VALIDATE_DEV" == "" ]; then
    VALIDATE_DEV=true
fi
if [ "$REPOSITORIES_CREATED" == "" ]; then
    REPOSITORIES_CREATED=true
fi
readonly VERBOSE_LEVEL
readonly VALIDATE_DEV
readonly REPOSITORIES_CREATED

readonly DOCKER_CONFIGURATION_PATH=/var/phpbenchmarks/componentFiles/.phpbenchmarks
