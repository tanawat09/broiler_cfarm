#!/usr/bin/env bash
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

php <<'PHP'
<?php

$keys = [
    'APP_NAME',
    'APP_ENV',
    'APP_KEY',
    'APP_DEBUG',
    'APP_URL',
    'APP_LOCALE',
    'APP_FALLBACK_LOCALE',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'SESSION_DRIVER',
    'CACHE_STORE',
    'QUEUE_CONNECTION',
    'MAIL_MAILER',
    'AUTO_SEED_DEMO_DATA',
    'DEMO_USER_PASSWORD',
    'ALLOW_DEMO_SEEDING',
];

$path = '.env';
$contents = file_exists($path) ? file_get_contents($path) : '';

foreach ($keys as $key) {
    $value = getenv($key);

    if ($value === false) {
        continue;
    }

    $escaped = '"' . str_replace(["\\", "\"", "\n", "\r"], ["\\\\", "\\\"", "\\n", ""], $value) . '"';
    $line = $key . '=' . $escaped;

    if (preg_match('/^' . preg_quote($key, '/') . '=/m', $contents)) {
        $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $contents);
    } else {
        $contents = rtrim($contents) . PHP_EOL . $line . PHP_EOL;
    }
}

file_put_contents($path, $contents);
PHP

if [ -z "${APP_KEY:-}" ] && ! grep -q '^APP_KEY=base64:' .env; then
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
elif [ "${AUTO_SEED_DEMO_DATA:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
else
    echo "Database has no users; demo seeding is disabled. Set AUTO_SEED_DEMO_DATA=true and DEMO_USER_PASSWORD to opt in."
fi

exec "$@"
