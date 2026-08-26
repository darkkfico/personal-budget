#!/bin/bash

set -euo pipefail

PORT="${PORT:-8080}"
export PORT
export SERVER_NAME=":${PORT}"

if [ -n "${DATABASE_URL:-}" ]; then
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

mkdir -p \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache \
  storage/framework/testing \
  storage/logs \
  bootstrap/cache

chmod -R ug+rwx storage bootstrap/cache || true

# Write Caddyfile with Railway's PORT so the healthcheck can connect.
cat > /tmp/Caddyfile <<EOF
{
	frankenphp
	auto_https off
}

http://0.0.0.0:${PORT} {
	root * /app/public
	encode gzip
	php_server
}
EOF

# Do not block the web server on artisan failures (missing APP_KEY/DB).
php artisan storage:link --force >/tmp/artisan-storage.log 2>&1 || true
php artisan migrate --force >/tmp/artisan-migrate.log 2>&1 || true
php artisan optimize >/tmp/artisan-optimize.log 2>&1 || true
php artisan schedule:work >/tmp/artisan-schedule.log 2>&1 &

echo "Starting FrankenPHP on 0.0.0.0:${PORT}"
exec docker-php-entrypoint --config /tmp/Caddyfile --adapter caddyfile
