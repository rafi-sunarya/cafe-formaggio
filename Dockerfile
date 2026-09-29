# ================================
# Stage 1 - Build frontend
# ================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build


# ================================
# Stage 2 - Laravel
# ================================
FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    bcmath \
    intl \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Apache document root → Laravel public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel project
COPY . .

# Copy compiled Vite assets
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Laravel storage permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

RUN chmod -R 775 \
    storage \
    bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]