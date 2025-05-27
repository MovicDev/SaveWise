FROM php:8.2-apache

# Install system dependencies and PHP extensions for PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy your PHP app code to container
COPY . /var/www/html/

# Run composer install inside the container during build
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Fix permissions so Apache can access files
RUN chown -R www-data:www-data /var/www/html
