#!/usr/bin/env bash

set -e

echo "127.0.0.1 benchmark-kit.loc" >> /etc/hosts
echo "127.0.0.1 statistics.benchmark-kit.loc" >> /etc/hosts

service php5.6-fpm start
service php7.0-fpm start
service php7.1-fpm start
service php7.2-fpm start
service php7.3-fpm start
service php7.4-fpm start

chmod 744 /var/log/php*.log

source /var/benchmark-kit/.env
if [ -f "/var/benchmark-kit/.env.local" ]; then
    source /var/benchmark-kit/.env.local
fi
sed -i "s/____PORT____/${NGINX_PORT}/g" /etc/nginx/sites-enabled/default

if [ "$1" == "--nginx-as-service" ]; then
    service nginx start
else
    nginx -g "daemon off;"
fi
