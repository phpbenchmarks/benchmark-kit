#!/usr/bin/env bash

# Assume that we are in vendor/bin directory. If you know how to get the real path of this script, don't hesitate ;)
readonly BENCHMARK_KIT_PATH=$(dirname $(cd `dirname $0` && pwd))"/phpbenchmarks/benchmark-kit"
source $BENCHMARK_KIT_PATH/common.sh
source $BENCHMARK_KIT_PATH/validation/configurationValidation.sh

function downloadGitHubFile {
    local file=$1
    local url="https://raw.githubusercontent.com/phpbenchmarks/$slugToCopy/$GITHUB_BRANCH/$file"
    local showValidationSuccess=true

    if [ $VERBOSE_LEVEL -ge 2 ]; then
        wget -O $INSTALLATION_PATH/$file $url
        [ $? != "0" ] && showValidationSuccess=false && rm $INSTALLATION_PATH/$file && echoValidationWarning "Error while downloading $url."
    else
        wget -O $INSTALLATION_PATH/$file $url &>/tmp/phpbenchmarks.download
        [ $? != "0" ] && showValidationSuccess=false && rm $INSTALLATION_PATH/$file && cat /tmp/phpbenchmarks.download && echoValidationWarning "Error while downloading $url."
    fi

    if [ $showValidationSuccess == true ]; then
        echoValidatedTest "[$file] File downloaded from github." "Downloaded"
    elif [ $VERBOSE_LEVEL -le 0 ]; then
        echo ""
    fi
}

function removeComponentVersionVariables {
    sed -i "/readonly PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION=/d" "$INSTALLATION_PATH/.phpbenchmarks/configuration.sh"
    sed -i "/readonly PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION=/d" "$INSTALLATION_PATH/.phpbenchmarks/configuration.sh"
    sed -i "/readonly PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION=/d" "$INSTALLATION_PATH/.phpbenchmarks/configuration.sh"
}

function downloadFilesFromGithub {
    echoAsk "Component slug to copy? (Example: symfony, laravel, cake-php)" false
    read slugToCopy

    echoAsk "Version to copy? (major.minor)" false
    read versionToCopy

    readonly GITHUB_BRANCH="$slugToCopy"_"$versionToCopy"_"$RESULT_TYPE_SLUG"
    echoValidationGroupStart "Downloading files from https://github.com/phpbenchmarks/$slugToCopy/tree/$GITHUB_BRANCH"
    downloadGitHubFile ".phpbenchmarks/configuration.sh"
    removeComponentVersionVariables
    downloadGitHubFile ".phpbenchmarks/initBenchmark.sh"
    downloadGitHubFile ".phpbenchmarks/vhost.conf"
    source $RESULT_TYPE_PATH/downloadFilesFromGithub.sh
    echoValidationGroupEnd
}

function createPhpbenchmarksDirectories {
    if [ ! -d "$INSTALLATION_PATH/.phpbenchmarks" ] || [ ! -d "$INSTALLATION_PATH/.phpbenchmarks/responseBody" ]; then
        echoValidationGroupStart "Create .phpbenchmarks directory"

        if [ ! -d "$INSTALLATION_PATH/.phpbenchmarks" ]; then
            mkdir "$INSTALLATION_PATH/.phpbenchmarks"
            echoValidatedTest "[.phpbenchmarks] Directory created."
        fi

        if [ ! -d "$INSTALLATION_PATH/.phpbenchmarks/responseBody" ]; then
            mkdir "$INSTALLATION_PATH/.phpbenchmarks/responseBody"
            echoValidatedTest "[.phpbenchmarks/responseBody] Directory created."
        fi

        echoValidationGroupEnd
    fi
}

function createCodeLinkFile {
    local codeLinkInstallationPath="$INSTALLATION_PATH/.phpbenchmarks/codeLink.sh"

    echoValidationGroupStart "Create .phpbenchmarks/codeLink.sh"

    source "$RESULT_TYPE_PATH/codeLink.sh"
    if [ -f "$INSTALLATION_PATH/.phpbenchmarks/codeLink.sh" ]; then
        source "$INSTALLATION_PATH/.phpbenchmarks/codeLink.sh"
    fi

    echo "#!/usr/bin/env bash" > $codeLinkInstallationPath
    echo "" >> $codeLinkInstallationPath
    echo "declare -A codeLinks=(" >> $codeLinkInstallationPath
    for codeLinkName in "${codeLink[@]}"; do
        echo "    [$codeLinkName]=\"${codeLinks[$codeLinkName]}\"" >> $codeLinkInstallationPath
        echoValidatedTest "\$codeLinks[$codeLinkName]=${codeLinks[$codeLinkName]}" "Writed"
    done
    echo ")" >> $codeLinkInstallationPath

    echoValidationGroupEnd
}

function defineVariableInConfigurationFile {
    local name=$1
    local value=$2
    local isString=$3
    local possibleValues=$4

    if [ "$value" == "" ]; then
        local askValueMessage="Value for \$$name?"
        if [ "$possibleValues" != "" ]; then
            askValueMessage="$askValueMessage ($possibleValues)"
        fi
        echoAsk "$askValueMessage"
        read value
    fi

    if [ "$isString" == "true" ]; then
        echo "readonly $name=\"$value\"" >> $INSTALLATION_PATH/.phpbenchmarks/configuration.sh
    else
        echo "readonly $name=$value" >> $INSTALLATION_PATH/.phpbenchmarks/configuration.sh
    fi
    [ $? != "0" ] && exitScript "Error while writing $name."
    echoValidatedTest "\$$name defined to $value." "Writed"
}

function createConfigurationFile {
    local configurationFilePath="$INSTALLATION_PATH/.phpbenchmarks/configuration.sh"

    echoValidationGroupStart "Create .phpbenchmarks/configuration.sh"

    source "$RESULT_TYPE_PATH/defaultConfiguration.sh"
    if [ -f "$configurationFilePath" ]; then
        source "$configurationFilePath"
    fi

    echo "#!/usr/bin/env bash" > $configurationFilePath
    echo "" >> $configurationFilePath

    defineVariableInConfigurationFile "PHPBENCHMARKS_PHP_5_6_ENABLED" "$PHPBENCHMARKS_PHP_5_6_ENABLED" false "true/false"
    defineVariableInConfigurationFile "PHPBENCHMARKS_PHP_7_0_ENABLED" "$PHPBENCHMARKS_PHP_7_0_ENABLED" false "true/false"
    defineVariableInConfigurationFile "PHPBENCHMARKS_PHP_7_1_ENABLED" "$PHPBENCHMARKS_PHP_7_1_ENABLED" false "true/false"
    defineVariableInConfigurationFile "PHPBENCHMARKS_PHP_7_2_ENABLED" "$PHPBENCHMARKS_PHP_7_2_ENABLED" false "true/false"
    defineVariableInConfigurationFile "PHPBENCHMARKS_PHP_7_3_ENABLED" "$PHPBENCHMARKS_PHP_7_3_ENABLED" false "true/false"

    echo "" >> $configurationFilePath
    defineVariableInConfigurationFile "PHPBENCHMARKS_COMPONENT_NAME" "$PHPBENCHMARKS_COMPONENT_NAME" true
    defineVariableInConfigurationFile "PHPBENCHMARKS_COMPONENT_SLUG" "$PHPBENCHMARKS_COMPONENT_SLUG" true

    echo "" >> $configurationFilePath
    defineVariableInConfigurationFile "PHPBENCHMARKS_BENCHMARK_URL" "$PHPBENCHMARKS_BENCHMARK_URL" true

    echo "" >> $configurationFilePath
    defineVariableInConfigurationFile "PHPBENCHMARKS_DEPENDENCY_NAME" "$PHPBENCHMARKS_DEPENDENCY_NAME" true

    echo "" >> $configurationFilePath
    defineVariableInConfigurationFile "PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION" "$PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION" false
    defineVariableInConfigurationFile "PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION" "$PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION" false
    defineVariableInConfigurationFile "PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION" "$PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION" false

    source "$configurationFilePath" &>/dev/null

    echoValidationGroupEnd
}

function createInitBenchmarkFile {
    local initBenchmarkPath="$INSTALLATION_PATH/.phpbenchmarks/initBenchmark.sh"

    if [ ! -f "$initBenchmarkPath" ]; then
        echoValidationGroupStart "Create .phpbenchmarks/initBenchmark.sh"

        cp "$BENCHMARK_KIT_PATH/validation/mainRepository/.phpbenchmarks/initBenchmark.sh" "$INSTALLATION_PATH/.phpbenchmarks/"
        [ $? != "0" ] && exitScript "Error while writing $initBenchmarkPath."
        echoValidatedTest "File created."
        echoValidatedTest "Function initBenchmark() created."
        echoValidationWarning ".phpbenchmarks/initBenchmark.sh::initBenchmark() has been created, but is nearly empty. Add commands to initialize benchmark here."

        if [ $VERBOSE_LEVEL -eq 0 ]; then
            echo ""
        fi
        echoValidationGroupEnd
    fi
}

function createVhostFile {
    local vhostPath="$INSTALLATION_PATH/.phpbenchmarks/vhost.conf"

    if [ ! -f "$vhostPath" ]; then
        echoValidationGroupStart "Create .phpbenchmarks/vhost.conf"

        cp "$BENCHMARK_KIT_PATH/validation/mainRepository/.phpbenchmarks/vhost.conf" "$INSTALLATION_PATH/.phpbenchmarks/"
        [ $? != "0" ] && exitScript "Error while writing $vhostPath."
        echoValidatedTest "File created."
        echoValidationWarning ".phpbenchmarks/vhost.conf has been created with default nginx virtual host configuration. Edit it if needed."

        if [ $VERBOSE_LEVEL -eq 0 ]; then
            echo ""
        fi
        echoValidationGroupEnd
    fi
}

createPhpbenchmarksDirectories

echoAsk "Copy configuration files from another location? [github/NONE]" false
read copy
copyFiles=false
if [ "$copy" == "github" ] || [ "$copy" == "github" ]; then
    copyFiles=true
    downloadFilesFromGithub
fi

if [ $copyFiles == false ]; then
    echo ""
fi

createCodeLinkFile
createConfigurationFile
createInitBenchmarkFile
createVhostFile
assertReadMe false
source $RESULT_TYPE_PATH/initializeBranch.sh
