#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY debe configurarse como variable de entorno persistente en Render." >&2
    exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force

if [ "${SYNC_DEMO_ACCOUNTS:-false}" = "true" ]; then
    echo "=== Sincronizando cuentas de demostración ==="
    php artisan db:seed --force
fi

php artisan storage:link --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
