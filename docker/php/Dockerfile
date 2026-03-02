FROM php:8.4-fpm-bookworm

# --------------------------------------------------
# System dependencies
# --------------------------------------------------
RUN apt-get update && apt-get -y upgrade && apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        intl \
        opcache \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# Node.js 20 (official method)
# --------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && npm cache clean --force

# --------------------------------------------------
# Composer
# --------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------------------------------
# PHP-FPM Configuration
# --------------------------------------------------
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# --------------------------------------------------
# Set working directory
# --------------------------------------------------
WORKDIR /var/www
COPY . .
# --------------------------------------------------
# Permissions (important for Laravel)
# --------------------------------------------------
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www

USER www-data
