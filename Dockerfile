FROM php:8.3-cli-alpine AS base

RUN apk add --no-cache \
    git \
    curl \
    wget \
    libpng \
    libpng-dev \
    libzip \
    libzip-dev \
    zip \
    unzip \
    oniguruma \
    oniguruma-dev \
    mysql-client \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && apk del --no-cache \
    libpng-dev \
    libzip-dev \
    oniguruma-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM base AS dependencies

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

FROM dependencies AS dev-dependencies

RUN composer install \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

FROM base AS production

COPY --from=dependencies /var/www/html/vendor /var/www/html/vendor

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

FROM base AS development

COPY --from=dev-dependencies /var/www/html/vendor /var/www/html/vendor

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

