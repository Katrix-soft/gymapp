# Stage 1: Build Frontend Assets
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Main Production Application
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    shadow

RUN docker-php-ext-install pdo_mysql gd zip bcmath opcache

# Copy composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configure system users & directory permissions
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Copy Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader

# Copy application code
COPY . .

# Copy compiled assets from builder
COPY --from=assets-builder /app/public/build ./public/build

# Setup configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh && \
    chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80

# Configure entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
