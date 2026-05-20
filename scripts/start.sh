#!/usr/bin/env bash

set -e

echo "==> Running Laravel deployment tasks..."
/bin/sh /var/www/html/scripts/00-laravel-deploy.sh

echo "==> Starting nginx + PHP-FPM via supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
