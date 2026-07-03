FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci && npm run build

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize

FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache bash mariadb-client libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql zip

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/broiler-entrypoint

RUN rm -f bootstrap/cache/*.php \
    && chmod +x /usr/local/bin/broiler-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["broiler-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
