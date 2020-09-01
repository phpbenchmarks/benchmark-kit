#!/usr/bin/env bash

docker ps | grep phpbenchmarks_benchmark-kit > /dev/null
if [ "$?" == 0 ]; then
    containerStarted=true
else
    containerStarted=false
fi

function exitScript() {
    if [ "$?" != "0" ]; then
        echo -e "\e[41m Error, script canceled. \e[0m"
    fi
}

set -e
trap exitScript EXIT

readonly ROOT_DIR=$(realpath $(dirname $(realpath $0)))
readonly CONTAINER_NAME="phpbenchmarks_benchmark-kit"
readonly SELFUPDATE_CONTAINER_NAME="${CONTAINER_NAME}_selfupdate"
readonly DEFAULT_CONFIG_PATH="/tmp/phpbenchmarkkit.default.sh"
readonly DOCKER_IMAGE_NAME="phpbenchmarks/benchmark-kit:4"
readonly BENCHMARK_KIT_PATH="/var/benchmark-kit"

function addHost() {
    local HOST="$1"
    if [ "$(cat /etc/hosts | grep -c $HOST)" -eq 0 ]; then
        echo -e "Add host \e[32m$HOST\e[0m."
        sudo /bin/sh -c "echo \"127.0.0.1 $HOST\" >> /etc/hosts"
    fi
}

function startContainer() {
    local defaultHostSourceCodePath=$(pwd)
    local defaultNginxPort="8080"
    if [ -f "$DEFAULT_CONFIG_PATH" ]; then
        source $DEFAULT_CONFIG_PATH
    fi

    if [ "$hostSourceCodePath" == "" ]; then
        echo -en "\e[44m Benchmark source code path [$defaultHostSourceCodePath]? \e[0m "
        read hostSourceCodePath
        if [ "$hostSourceCodePath" == "" ]; then
            hostSourceCodePath=$defaultHostSourceCodePath
        fi
    else
        echo -e "Source code: \e[32m$hostSourceCodePath\e[0m."
    fi
    if [ ! -d "$hostSourceCodePath" ]; then
        echo -e "\e[41m Benchmark source code path $hostSourceCodePath is not a redirectory. \e[0m"
        exit 1
    fi

    if [ "$nginxPort" == "" ]; then
        echo -en "\e[44m Nginx port [$defaultNginxPort]? \e[0m "
        read nginxPort
        if [ "$nginxPort" == "" ]; then
            nginxPort=$defaultNginxPort
        fi
    else
        echo -e "Nginx port: \e[32m$nginxPort\e[0m."
    fi

    echo "#!/usr/bin/env bash" > $DEFAULT_CONFIG_PATH
    echo "defaultHostSourceCodePath=$hostSourceCodePath" >> $DEFAULT_CONFIG_PATH
    echo "defaultNginxPort=$nginxPort" >> $DEFAULT_CONFIG_PATH

    if [ $kitAsVolume == true ]; then
        kitAsVolumeDockerRunParameter="-v $ROOT_DIR:$BENCHMARK_KIT_PATH"
    else
        kitAsVolumeDockerRunParameter=""
    fi

    echo -e "Start \e[32m$CONTAINER_NAME\e[0m container."
    docker run \
        $ttyParameter \
        -d \
        --name=$CONTAINER_NAME \
        --rm \
        -p 127.0.0.1:$nginxPort:$nginxPort \
        -v $hostSourceCodePath:/var/www/benchmark \
        $kitAsVolumeDockerRunParameter \
        $DOCKER_IMAGE_NAME \
        > /dev/null

    docker exec -it $CONTAINER_NAME /bin/bash -c "echo NGINX_PORT=$nginxPort > $BENCHMARK_KIT_PATH/.env.local"
    docker exec -it $CONTAINER_NAME /bin/bash -c "echo HOST_SOURCE_CODE_PATH=$hostSourceCodePath >> $BENCHMARK_KIT_PATH/.env.local"

    containerStarted=true
}

function updatePhpBenchKitScript()
{
    set +e
    docker stop phpbenchmarks_benchmark-kit_selfupdate > /dev/null 2>&1
    set -e

    docker run \
        -it \
        -d \
        --name=$SELFUPDATE_CONTAINER_NAME \
        --rm \
        $DOCKER_IMAGE_NAME

    local binPath="$ROOT_DIR/$(basename $0)"
    echo -en "\e[43m sudo password could be asked to update $binPath \e[0m\n"
    sudo docker cp $SELFUPDATE_CONTAINER_NAME:$BENCHMARK_KIT_PATH/phpbenchkit.sh "$binPath"
    docker stop $SELFUPDATE_CONTAINER_NAME
}

function stopContainer() {
    if [ $containerStarted == true ]; then
        echo -e "Stop \e[32m$CONTAINER_NAME\e[0m container."
        set +e
        docker kill $CONTAINER_NAME
        set -e
    fi
}

stopContainer=false
restartContainer=false
kitAsVolume=false
consoleParams=""
selfUpdate=false
hostSourceCodePath=""
nginxPort=""
tty=true
for param in "$@"; do
    if [ "$param" == "--stop" ]; then
        stopContainer=true
    elif [ "$param" == "--restart" ]; then
        restartContainer=true
    elif [ "$param" == "--dev" ]; then
        kitAsVolume=true
    elif [ "$param" == "--selfupdate" ]; then
        selfUpdate=true
    elif [ "${param:0:9}" == "--source=" ]; then
        hostSourceCodePath=${param:9}
        restartContainer=true
    elif [ "${param:0:13}" == "--nginx-port=" ]; then
        nginxPort=${param:13}
        restartContainer=true
    elif [ "$param" == "--no-tty" ]; then
        tty=false
    else
        consoleParams="$consoleParams $param"
    fi
done

if [ $tty == true ]; then
    ttyParameter="-it"
else
    ttyParameter="-t"
fi

if [ $selfUpdate == true ]; then
    stopContainer
    set +e
    docker rmi --force $(docker images --format '{{.Repository}}:{{.Tag}}' | grep phpbenchmarks/benchmark-kit)
    set -e
    updatePhpBenchKitScript
    exit 0
fi

if [ $stopContainer == true ]; then
    stopContainer
    exit 0
fi

if [ $restartContainer == true ]; then
    stopContainer
    startContainer
fi

if [ "$containerStarted" == false ]; then
    startContainer
fi

addHost "benchmark-kit.loc"
addHost "phpinfo.benchmark-kit.loc"
addHost "statistics.benchmark-kit.loc"
addHost "preload-generator.benchmark-kit.loc"

docker exec $ttyParameter --user=phpbenchmarks $CONTAINER_NAME phpbenchkit $consoleParams
