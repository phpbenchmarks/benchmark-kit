#!/usr/bin/env bash

docker ps | grep phpbenchmarks_benchmark-kit > /dev/null
CONTAINER_STARTED=$?

function exitScript() {
    echo -e "\e[41m Error, script canceled. \e[0m"
}

set -e
trap exitScript ERR

readonly CONTAINER_NAME="phpbenchmarks_benchmark-kit"
readonly DEFAULT_CONFIG_PATH="/tmp/phpbenchmarkkit.default.sh"

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

    docker run \
        -it \
        -d \
        --name=phpbenchmarks_benchmark-kit \
        --rm \
        -p 127.0.0.1:${nginxPort}:80 \
        -v ${sourceCodePath}:/var/www/benchmark \
        -e NGINX_PORT=${nginxPort} \
        phpbenchmarks/benchmark-kit

    if [ -f "${sourceCodePath}/.phpbenchmarks/vhost.conf" ]; then
        echo -e "\e[32mCreate vhosts.\e[0m"
        docker exec -it --user=phpbenchmarks phpbenchmarks_benchmark-kit bin/console vhost:create
    fi
}

function stopContainer()
{
    if [ ${CONTAINER_STARTED} == 0 ]; then
        echo -e "\e[32mStop ${CONTAINER_NAME} container...\e[0m"
        docker stop phpbenchmarks_benchmark-kit
    fi
}

if [ "$1" == "--stop" ]; then
    stopContainer
    exit 0
fi

if [ "$1" == "--restart" ]; then
    stopContainer
    startContainer
    exit 0
fi

if [ "${CONTAINER_STARTED}" == 1 ]; then
    startContainer
fi

docker exec -it --user=phpbenchmarks phpbenchmarks_benchmark-kit bin/console $@
