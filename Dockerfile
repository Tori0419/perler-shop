FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT=/var/www/html/public \
    RUN_SCRIPTS=1 \
    SKIP_COMPOSER=1 \
    REAL_IP_HEADER=X-Forwarded-For \
    LOG_STDOUT=true \
    LOG_STDERR=true \
    APP_ENV=production \
    APP_DEBUG=false \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY --chown=nginx:nginx . /var/www/html

RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh
