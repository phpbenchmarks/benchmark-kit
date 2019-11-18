#!/usr/bin/env bash

set -e

echo "127.0.0.1 benchmark-kit.loc" >> /etc/hosts

service php5.6-fpm start
service php7.0-fpm start
service php7.1-fpm start
service php7.2-fpm start
service php7.3-fpm start

if [ "$1" == "--nginx-as-service" ]; then
    service nginx start
else
    nginx -g "daemon off;"
fi
