#!/usr/bin/env bash

function onExit {
    if [ -f /tmp/phpbenchmarks.docker ]; then
        rm /tmp/phpbenchmarks.docker
    fi
}

function exitScript {
    echo ""
    echo -e "\e[41m ERROR \e[00m \e[31m$1\e[00m"
    exit 1
}

function echoAction {
    currentAction=$1
    echo "$currentAction ..."
}

function echoActionDone {
    echo -en "\e[1A"
    echo -e "$currentAction ... \e[32mdone\e[0m"
}

function echoAsk {
    local message=$1
    echo -n -e "\e[45m $message \e[00m "
}

function defineInstallationPath {
    if [ "$1" != "" ]; then
        installationPath=$1
    else
        question="Path to your code?"
        if [ -d "$lastInstallationPath" ]; then
            question="$question [$lastInstallationPath]"
        fi
        echoAsk "$question"
        read installationPath
        if [ "$installationPath" == "" ]; then
            installationPath=$lastInstallationPath
        fi
    fi

    if [ ! -d "$installationPath" ]; then
        exitScript "Invalid path."
    fi

    echo "#!/usr/bin/env bash" > $lastConfigurationPath
    echo "readonly lastInstallationPath=$installationPath" >> $lastConfigurationPath
}

function defineNginxPort {
    if [ "$lastNginxPort" != "" ]; then
        readonly defaultNginxPort="$lastNginxPort"
    else
        readonly defaultNginxPort="80"
    fi

    echoAsk "Port of nginx? [$defaultNginxPort]"
    read nginxPort

    if [ "$nginxPort" == "" ]; then
        nginxPort=$defaultNginxPort
    fi

    echo "readonly lastNginxPort=$nginxPort" >> $lastConfigurationPath
}

function createPhpbenchmarksDirectory {
    if [ ! -d "$installationPath/.phpbenchmarks" ]; then
        echoAction "Create $installationPath/.phpbenchmarks directory"
        mkdir "$installationPath/.phpbenchmarks"
        [ $? != "0" ] && exitScript
        echoActionDone
    fi
}

function buildDockerImage {
    echoAction "Building Docker image, it may take a few minutes"

    cp .env.dist .env
    [ $? != "0" ] && exitScript

    sed -i -e "s~____INSTALLATION_PATH____~$installationPath~g" .env
    [ $? != "0" ] && exitScript

    sed -i -e "s~____DOCKER_UID____~$UID~g" .env
    [ $? != "0" ] && exitScript

    sed -i -e "s~____NGINX_PORT____~$nginxPort~g" .env
    [ $? != "0" ] && exitScript

    docker-compose up --build --no-start phpbenchmarks_benchmark_kit &>/tmp/phpbenchmarks.docker
    [ $? != "0" ] && cat /tmp/phpbenchmarks.docker && exitScript "Building Docker image failed."

    echoActionDone
}

function startDockerContainer {
    echoAction "Starting Docker container"
    docker-compose up -d phpbenchmarks_benchmark_kit &>/tmp/phpbenchmarks.docker
    [ $? != "0" ] && cat /tmp/phpbenchmarks.docker && exitScript
    echoActionDone
}

function addHost() {
    local host=$1
    if [ "$(cat /etc/hosts | grep $host)" == "" ]; then
        echoAction "Add $host host in /etc/hosts"
        sudo bash -c "echo '127.0.0.1       $host' >> /etc/hosts"
        [ "$?" != "0" ] && exitScript "Error while adding host $host."
        echoActionDone
    fi
}

trap onExit EXIT
if [ -d "vendor/phpbenchmarks/benchmark-kit" ]; then
    readonly KIT_ROOT_PATH="vendor/phpbenchmarks/benchmark-kit"
else
    readonly KIT_ROOT_PATH="$(dirname $0)"
fi

currentAction=

lastConfigurationPath="$(dirname $0)/var/lastConfiguration.sh"
if [ -f "$lastConfigurationPath" ]; then
    source $lastConfigurationPath
fi

defineInstallationPath
defineNginxPort

createPhpbenchmarksDirectory

addHost "php56.benchmark.loc"
addHost "php70.benchmark.loc"
addHost "php71.benchmark.loc"
addHost "php72.benchmark.loc"
addHost "php73.benchmark.loc"

cd "$KIT_ROOT_PATH/docker"
buildDockerImage
startDockerContainer
cd - 1>/dev/null

source "$KIT_ROOT_PATH/dockerBash.sh"
