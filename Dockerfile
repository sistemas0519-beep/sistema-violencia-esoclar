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
        xml \
        dom \
        fileinfo \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

WORKDIR /app

COPY . .

RUN [ -f .env ] || cp .env.example .env

RUN mkdir -p /app/storage/logs /app/storage/framework/cache \
        /app/storage/framework/sessions /app/storage/framework/views \
        /app/database \
    && touch /app/database/database.sqlite \
    && chmod -R 775 /app/storage /app/bootstrap/cache

RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

RUN php artisan key:generate --force
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan config:clear || true
RUN php artisan octane:install --server="swoole" --no-interaction || true

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]