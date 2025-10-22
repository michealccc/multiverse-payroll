FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    curl \
    zip \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN if [ -s composer.json ]; then \
        composer install --optimize-autoloader --no-interaction; \
    fi

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]