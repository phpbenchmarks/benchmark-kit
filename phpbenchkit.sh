#!/usr/bin/env bash

docker ps | grep phpbenchmarks_benchmark-kit > /dev/null
CONTAINER_STARTED=$?

function exitScript() {
    echo -e "\e[41m Error, script canceled. \e[0m"
}

set -e
trap exitScript ERR

readonly ROOT_DIR=$(realpath $(dirname $(realpath $0)))
readonly CONTAINER_NAME="phpbenchmarks_benchmark-kit"
readonly DEFAULT_CONFIG_PATH="/tmp/phpbenchmarkkit.default.sh"

function addHost() {
    local HOST="benchmark-kit.loc"
    if [ "$(cat /etc/hosts | grep -c ${HOST})" -eq 0 ]; then
        echo -e "\e[32mAdd host ${HOST}\e[0m..."
        sudo /bin/sh -c "echo \"127.0.0.1 ${HOST}\" >> /etc/hosts"
    fi
}

function startContainer() {
    echo -e "\e[32mStart ${CONTAINER_NAME} container...\e[0m"

    defaultSourceCodePath=$(pwd)
    defaultNginxPort="8080"
    if [ -f "${DEFAULT_CONFIG_PATH}" ]; then
        source ${DEFAULT_CONFIG_PATH}
    fi

    echo -en "\e[44m Benchmark source code path [${defaultSourceCodePath}]? \e[0m "
    read sourceCodePath
    if [ "${sourceCodePath}" == "" ]; then
        sourceCodePath=${defaultSourceCodePath}
    fi
    if [ ! -d ${sourceCodePath} ]; then
        echo "Source code path ${sourceCodePath} is not a redirectory."
        exit 1
    fi

    echo -en "\e[44m Nginx port [${defaultNginxPort}]? \e[0m "
    read nginxPort
    if [ "${nginxPort}" == "" ]; then
        nginxPort=${defaultNginxPort}
    fi

    echo "#!/usr/bin/env bash" > ${DEFAULT_CONFIG_PATH}
    echo "defaultSourceCodePath=${sourceCodePath}" >> ${DEFAULT_CONFIG_PATH}
    echo "defaultNginxPort=${nginxPort}" >> ${DEFAULT_CONFIG_PATH}

    if [ ${paramDev} == true ]; then
        benchmarkKitVolume="-v ${ROOT_DIR}:/var/benchmark-kit"
    else
        benchmarkKitVolume=""
    fi

    docker run \
        -it \
        -d \
        --name=phpbenchmarks_benchmark-kit \
        --rm \
        -p 127.0.0.1:${nginxPort}:80 \
        -v ${sourceCodePath}:/var/www/benchmark \
        ${benchmarkKitVolume} \
        -e NGINX_PORT=${nginxPort} \
        phpbenchmarks/benchmark-kit
}

function stopContainer() {
    if [ ${CONTAINER_STARTED} == 0 ]; then
        echo -e "\e[32mStop ${CONTAINER_NAME} container...\e[0m"
        docker stop phpbenchmarks_benchmark-kit
    fi
}

paramStop=false
paramRestart=false
paramDev=false
consoleParams=""
for param in "$@"; do
    if [ "$param" == "--stop" ]; then
        paramStop=true
    elif [ "$param" == "--restart" ]; then
        paramRestart=true
    elif [ "$param" == "--dev" ]; then
        paramDev=true
    else
        consoleParams="${consoleParams} $param"
    fi
done


if [ $paramStop == true ]; then
    stopContainer
    exit 0
fi

if [ $paramRestart == true ]; then
    stopContainer
    startContainer
elif [ "${CONTAINER_STARTED}" == 1 ]; then
    startContainer
fi

addHost

docker exec -it --user=phpbenchmarks phpbenchmarks_benchmark-kit /usr/bin/php7.3 bin/console ${consoleParams}
