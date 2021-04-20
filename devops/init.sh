#!/bin/sh
set -e

echo "Fetching config from Consul..."
value=$(curl --fail -Ss http://$CONSUL_URL/v1/kv/$CONSUL_ENV)
echo $value | jq -r '.[0].Value' | base64 -d > /var/www/app/.env

#/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
nginx -g "daemon off;" &
php-fpm
