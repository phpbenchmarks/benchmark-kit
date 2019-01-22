#!/usr/bin/env bash

source common.sh
source validation/configurationValidation.sh

function isValidUrl {
    local url=$1

    if [ "$url" == "" ] || ([ "$url" != "http://" ] && [ "$url" != "https://" ]); then
        return 1
    fi

    return 0
}

function validateCodeLinks {
    hasNewValues=false
    declare -A newCodeLinks
    for codeLinkKey in "${!codeLinkNames[@]}"; do
        if ! isValidUrl "${codeLinks[$codeLinkKey]}"; then
            hasNewValues=true
            invalidUrl=true
            while [ $invalidUrl == true ]; do
                echoWarningAsk "${codeLinkNames[$codeLinkKey]}"
                read sourceCodeUrl

                if ! isValidUrl "$sourceCodeUrl"; then
                    echoError "URL is invalid."
                else
                    invalidUrl=false
                    newCodeLinks[$codeLinkKey]="$sourceCodeUrl"
                fi
            done
        else
            newCodeLinks[$codeLinkKey]="${codeLinks[$codeLinkKey]}"
            echoValidatedTest "\$codeLink[$codeLinkKey] is defined (${codeLinks[$codeLinkKey]})"
        fi
    done

    if [ $hasNewValues == true ]; then
        echoWarningAsk "Write .phpbenchmarks/codeLink.sh with new values? [Y/n]"
        read writeCodeLinkFile
        if [ "$writeCodeLinkFile" == "" ] || [ "$writeCodeLinkFile" == "y" ] || [ "$writeCodeLinkFile" == "Y" ]; then
            codeLinkInstallationPath="$INSTALLATION_PATH/.phpbenchmarks/codeLink.sh"
            echo "#!/usr/bin/env bash" > $codeLinkInstallationPath
            echo "" >> $codeLinkInstallationPath
            echo "declare -A codeLinks=(" >> $codeLinkInstallationPath
            for newCodeLinkKey in "${!newCodeLinks[@]}"; do
                echo "    [$newCodeLinkKey]=\"${newCodeLinks[$newCodeLinkKey]}\"" >> $codeLinkInstallationPath
            done
            echo ")" >> $codeLinkInstallationPath
        fi

        return 1
    fi

    return 0
}
echoValidationGroupStart "Validation of .phpbenchmarks directory"
copyConfigurationFiles "$CONFIGURATION_PATH"
assertCommonConfiguration
source "$RESULT_TYPE_PATH/configurationValidation.sh"
echoValidationGroupEnd

sourceCodeUrlLabel="source code url?"
declare -A codeLinkNames=(
    [route]="Route definition $sourceCodeUrlLabel"
    [controller]="Controller method $sourceCodeUrlLabel"
)

echoValidationGroupStart "Validation of .phpbenchmarks/codeLink.sh"
assertConfigurationFileExist "codeLink.sh"
source "$CONFIGURATION_PATH/codeLink.sh"

while ! validateCodeLinks; do
    cp "$INSTALLATION_PATH/.phpbenchmarks/codeLink.sh" $CONFIGURATION_PATH/
    source "$CONFIGURATION_PATH/codeLink.sh"
    echoValidationGroupEnd

    echoValidationGroupStart "Validation of .phpbenchmarks/codeLink.sh"
done

echoValidationGroupEnd
