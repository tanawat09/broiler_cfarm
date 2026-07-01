#!/usr/bin/env bash
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

set_env_value() {
    local key="$1"
    local value="$2"

    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        printf '\n%s=%s\n' "$key" "$value" >> .env
    fi
}

set_env_value APP_ENV "${APP_ENV:-local}"
set_env_value APP_DEBUG "${APP_DEBUG:-true}"
set_env_value APP_URL "${APP_URL:-http://localhost:8000}"
set_env_value DB_CONNECTION "${DB_CONNECTION:-mysql}"
set_env_value DB_HOST "${DB_HOST:-db}"
set_env_value DB_PORT "${DB_PORT:-3306}"
set_env_value DB_DATABASE "${DB_DATABASE:-broiler}"
set_env_value DB_USERNAME "${DB_USERNAME:-broiler}"
set_env_value DB_PASSWORD "${DB_PASSWORD:-broiler_password}"
set_env_value SESSION_DRIVER "${SESSION_DRIVER:-database}"
set_env_value CACHE_STORE "${CACHE_STORE:-database}"
set_env_value QUEUE_CONNECTION "${QUEUE_CONNECTION:-database}"

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
    echo "Waiting for MariaDB..."
    sleep 2
done

php artisan migrate --force --no-interaction

USER_COUNT="$(mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" -N -B "${DB_DATABASE}" -e "SELECT COUNT(*) FROM users;" 2>/dev/null || echo 0)"

if [ "${USER_COUNT}" != "0" ]; then
    echo "Database already has users; skipping seed."
else
    php artisan db:seed --force --no-interaction
fi

exec "$@"
