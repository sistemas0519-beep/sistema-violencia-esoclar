FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        sockets \
        pcntl \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

WORKDIR /app

# Copiar solo archivos de dependencias primero (mejor cache de capas)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copiar el resto del código
COPY . .

# Instalar octane y roadrunner si no están en composer.json
RUN composer require laravel/octane spiral/roadrunner --no-interaction --no-update 2>/dev/null || true
RUN composer update --no-interaction --prefer-dist --optimize-autoloader

COPY .env.example .env

RUN mkdir -p /app/storage/logs /app/storage/framework/cache \
        /app/storage/framework/sessions /app/storage/framework/views \
        /app/database \
    && touch /app/database/database.sqlite \
    && chmod -R 775 /app/storage /app/bootstrap/cache

RUN php artisan key:generate --force
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan config:clear || true
RUN php artisan octane:install --server="swoole" --no-interaction || true

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]