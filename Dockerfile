FROM node:24-bookworm-slim AS assets

WORKDIR /app

COPY package.json ./
RUN npm install

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


FROM composer:latest AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction


FROM dunglas/frankenphp:php8.4-trixie

WORKDIR /app

RUN install-php-extensions pcntl pdo_pgsql zip opcache

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/framework/testing \
        storage/logs \
        bootstrap/cache \
        public \
    && printf 'ok' > public/healthz \
    && chmod -R a+rw storage bootstrap/cache \
    && sed -i 's/\r$//' /app/start-container.sh /app/Caddyfile \
    && chmod +x /app/start-container.sh

ENV SERVER_NAME=:8080

EXPOSE 8080

CMD ["/app/start-container.sh"]
