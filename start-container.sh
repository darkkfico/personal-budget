#!/bin/bash

set -e

export SERVER_NAME=":${PORT:-80}"

# Railway Postgres/MySQL plugins expose DATABASE_URL; Laravel reads DB_URL.
if [ -n "${DATABASE_URL}" ]; then
  export DB_URL="${DB_URL:-$DATABASE_URL}"

  case "${DATABASE_URL}" in
    mysql*|mariadb*)
      export DB_CONNECTION="${DB_CONNECTION:-mysql}"
      ;;
    *)
      export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
      ;;
  esac
fi

if [ -z "${APP_KEY}" ]; then
  echo "APP_KEY is not set. Generate one with 'php artisan key:generate --show' and add it in Railway variables."
  exit 1
fi

mkdir -p \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache \
  storage/framework/testing \
  storage/logs \
  bootstrap/cache

if [ "${RAILPACK_SKIP_MIGRATIONS}" != "true" ]; then
  echo "Running migrations ..."
  php artisan migrate --force
fi

php artisan storage:link --force
php artisan optimize:clear
php artisan optimize

echo "Starting Laravel scheduler ..."
php artisan schedule:work &

echo "Starting Laravel server ..."

if [ -f /app/Caddyfile ]; then
  exec docker-php-entrypoint --config /app/Caddyfile --adapter caddyfile
fi

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile
