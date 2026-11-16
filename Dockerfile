# NT Sistemas Web · site + painel (Laravel + Livewire).
# Base: serversideup/php (Nginx + PHP-FPM, pronta para Laravel e Coolify). Mesmo modelo do NT Omni.

############################
# 1) Assets (Vite)
############################
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

############################
# 2) Dependências PHP
############################
FROM serversideup/php:8.4-fpm-nginx AS vendor
USER root
RUN install-php-extensions intl bcmath
USER www-data
WORKDIR /var/www/html
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --no-autoloader

############################
# 3) Imagem final
############################
FROM serversideup/php:8.4-fpm-nginx

USER root
RUN install-php-extensions intl bcmath

# AUTORUN: ao subir, roda as migrations e o cache de config/rotas/views.
ENV PHP_OPCACHE_ENABLE=1 \
    SSL_MODE=off \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true \
    HEALTHCHECK_PATH=/up

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

USER www-data
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && php artisan package:discover --ansi

EXPOSE 8080
