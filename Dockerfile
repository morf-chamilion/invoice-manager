# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 — Front-end assets (Laravel Mix / webpack 5)
# ---------------------------------------------------------------------------
FROM node:18-alpine AS assets

WORKDIR /app

# webpack 5 under Node 17+ / OpenSSL 3 fails with ERR_OSSL_EVP_UNSUPPORTED
ENV NODE_OPTIONS=--openssl-legacy-provider

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY webpack.mix.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

# Outputs public/js, public/css, public/assets and public/mix-manifest.json
RUN npm run production

# ---------------------------------------------------------------------------
# Stage 2 — PHP base: FrankenPHP (PHP embedded in Caddy) plus the extension
# set both later stages need.
#
# Already compiled into dunglas/frankenphp:php8.3-alpine and therefore not
# installed here:
#   ctype curl dom fileinfo filter hash iconv json libxml mbstring openssl
#   pcre pdo phar posix session simplexml sodium tokenizer xml zlib opcache
#
# Installed below, with what requires them:
#   exif       spatie/image, spatie/laravel-medialibrary   (hard composer req)
#   zip        spatie/laravel-backup                       (hard composer req)
#   gd         medialibrary conversions (IMAGE_DRIVER=gd) + dompdf images
#   pdo_mysql  DB_CONNECTION=mysql
#   pcntl      graceful signal handling for schedule:work / queue:work
#   bcmath     precise money arithmetic
#   intl       number/date formatting on invoices
#
# install-php-extensions ships in the FrankenPHP image, and builds each
# extension against this image's thread-safe (ZTS) PHP.
# ---------------------------------------------------------------------------
FROM dunglas/frankenphp:php8.3-alpine AS php-base

RUN install-php-extensions \
        exif \
        gd \
        zip \
        pdo_mysql \
        pcntl \
        bcmath \
        intl

WORKDIR /app

# ---------------------------------------------------------------------------
# Stage 3 — Composer vendor tree
# Built on php-base so `composer install` sees the real extension set and its
# platform check passes without --ignore-platform-reqs.
# ---------------------------------------------------------------------------
FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress

COPY . .

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts

# ---------------------------------------------------------------------------
# Stage 4 — Runtime: a single FrankenPHP process serving HTTP and running PHP
# ---------------------------------------------------------------------------
FROM php-base AS runtime

# bash      entrypoint shebang
# su-exec   drop root after the entrypoint has fixed volume ownership
# mysql-client  provides mysqldump, required by spatie/laravel-backup
RUN apk add --no-cache \
        bash \
        su-exec \
        mysql-client \
        tzdata

COPY docker/php.ini      /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/Caddyfile    /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

COPY --chown=www-data:www-data . .
COPY --from=vendor  --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets  --chown=www-data:www-data /app/public ./public

RUN mkdir -p \
        storage/app/public \
        storage/app/media \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

# Caddy initialises a storage backend even with auto_https off, so give it a
# directory www-data can write to rather than letting it probe root's $HOME.
ENV XDG_DATA_HOME=/var/lib/caddy \
    XDG_CONFIG_HOME=/var/lib/caddy
RUN mkdir -p /var/lib/caddy && chown -R www-data:www-data /var/lib/caddy

# Bake the package manifest so boot doesn't have to rediscover packages.
# No `|| true`: provider boot no longer requires a database, so this genuinely
# has to succeed here rather than failing quietly into a runtime rebuild.
RUN php artisan package:discover --ansi

ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
