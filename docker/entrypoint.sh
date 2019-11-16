#!/usr/bin/env bash

set -e

echo "127.0.0.1 php56.benchmark.loc" >> /etc/hosts
echo "127.0.0.1 php70.benchmark.loc" >> /etc/hosts
echo "127.0.0.1 php71.benchmark.loc" >> /etc/hosts
echo "127.0.0.1 php72.benchmark.loc" >> /etc/hosts
echo "127.0.0.1 php73.benchmark.loc" >> /etc/hosts

service php5.6-fpm start
service php7.0-fpm start
service php7.1-fpm start
service php7.2-fpm start
service php7.3-fpm start

if [ "$1" == "--nginx-as-daemon" ]; then
    nginx -g "daemon off;"
else
    service nginx start
fi
