#!/bin/sh
set -eu

cd /var/www/html

resolve_path() {
  configured_path="$1"
  if [ -z "${configured_path}" ]; then
    echo ""
  elif [ "${configured_path#"/"}" != "${configured_path}" ]; then
    echo "${configured_path}"
  else
    echo "/var/www/html/${configured_path}"
  fi
}

SHOP_DATA_DIR_PATH="$(resolve_path "${SHOP_DATA_DIR:-storage/app/data}")"
SHOP_UPLOAD_DIR_PATH="$(resolve_path "${SHOP_UPLOAD_DIR:-public/images/uploads/products}")"

if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  else
    touch .env
  fi
fi

mkdir -p \
  storage/app/data \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  public/images/uploads/products \
  "${SHOP_DATA_DIR_PATH}" \
  "${SHOP_UPLOAD_DIR_PATH}"

if [ "${DEMO_RESET_ON_BOOT:-false}" = "true" ]; then
  rm -f "${SHOP_DATA_DIR_PATH}/orders.json" "${SHOP_DATA_DIR_PATH}/products.json"
  find "${SHOP_UPLOAD_DIR_PATH}" -mindepth 1 -type f ! -name '.gitkeep' -delete || true
fi

if [ -z "${APP_KEY:-}" ]; then
  APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
  export APP_KEY
fi

php -r '$path=".env"; $env=file_exists($path) ? file_get_contents($path) : ""; $key=(string) getenv("APP_KEY"); if ($key === "") { exit(0); } if (preg_match("/^APP_KEY=/m", $env)) { $env = preg_replace("/^APP_KEY=.*/m", "APP_KEY=".$key, $env); } else { $env .= (trim($env)==="" ? "" : PHP_EOL)."APP_KEY=".$key; } file_put_contents($path, rtrim($env).PHP_EOL);'

chmod -R ug+rwx storage bootstrap/cache "${SHOP_UPLOAD_DIR_PATH}" || true

# Clear and rebuild caches on every start
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true

PORT_TO_USE="${PORT:-10000}"
exec php -S "0.0.0.0:${PORT_TO_USE}" -t public public/server.php
