#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

resolve_path() {
  local configured_path="$1"
  if [ -z "${configured_path}" ]; then
    echo ""
    return
  fi

  if [[ "${configured_path}" = /* ]]; then
    echo "${configured_path}"
  else
    echo "/var/www/html/${configured_path}"
  fi
}

SHOP_DATA_DIR_PATH="$(resolve_path "${SHOP_DATA_DIR:-storage/app/data}")"
SHOP_UPLOAD_DIR_PATH="$(resolve_path "${SHOP_UPLOAD_DIR:-public/images/uploads/products}")"

if [ ! -f .env ]; then
  cp .env.example .env
fi

mkdir -p \
  storage/app/data \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  public/images/uploads/products

mkdir -p "${SHOP_DATA_DIR_PATH}" "${SHOP_UPLOAD_DIR_PATH}"

if [ "${DEMO_RESET_ON_BOOT:-false}" = "true" ]; then
  rm -f "${SHOP_DATA_DIR_PATH}/orders.json" "${SHOP_DATA_DIR_PATH}/products.json"
  find "${SHOP_UPLOAD_DIR_PATH}" -mindepth 1 -type f ! -name '.gitkeep' -delete || true
fi

composer install \
  --working-dir=/var/www/html \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ -z "${APP_KEY:-}" ]; then
  if [ -f /var/data/app_key ]; then
    APP_KEY="$(cat /var/data/app_key)"
  else
    APP_KEY="$(php artisan key:generate --show)"
    if [ -d /var/data ]; then
      printf "%s" "${APP_KEY}" > /var/data/app_key
    fi
  fi
  export APP_KEY
fi

php -r '$path=".env"; $env=file_exists($path)?file_get_contents($path):""; $pairs=["APP_ENV"=>getenv("APP_ENV")?:"production","APP_DEBUG"=>getenv("APP_DEBUG")?:"false","APP_URL"=>getenv("APP_URL")?: (getenv("RENDER_EXTERNAL_URL")?:""),"APP_KEY"=>getenv("APP_KEY")?:"","DEMO_RESET_ON_BOOT"=>getenv("DEMO_RESET_ON_BOOT")?:"false","SHOP_DATA_DIR"=>getenv("SHOP_DATA_DIR")?:"storage/app/data","SHOP_UPLOAD_DIR"=>getenv("SHOP_UPLOAD_DIR")?:"public/images/uploads/products","SHOP_UPLOAD_PUBLIC_PREFIX"=>getenv("SHOP_UPLOAD_PUBLIC_PREFIX")?:"/images/uploads/products"]; foreach($pairs as $k=>$v){ if($v===""){ continue; } if(preg_match("/^".preg_quote($k,"/")."=/m",$env)){ $env=preg_replace("/^".preg_quote($k,"/")."=.*/m",$k."=".$v,$env); } else { $env .= (trim($env)==="" ? "" : PHP_EOL).$k."=".$v; } } file_put_contents($path,rtrim($env).PHP_EOL);'

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R ug+rwx storage bootstrap/cache "${SHOP_UPLOAD_DIR_PATH}" || true
chown -R nginx:nginx storage bootstrap/cache "${SHOP_UPLOAD_DIR_PATH}" || true
