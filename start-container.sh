#!/bin/bash

set -e

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

if [ "$IS_LARAVEL" = "true" ]; then
  if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
    echo "Running migrations ..."
    php artisan migrate --force
  fi

  php artisan storage:link --force
  php artisan optimize:clear
  php artisan optimize

  echo "Starting Laravel scheduler ..."
  php artisan schedule:work &

  echo "Starting Laravel server ..."
fi

# Start the FrankenPHP server
docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
