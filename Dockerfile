FROM php:8.2-apache

# Install required system packages first
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your PHP app into the container
COPY . /var/www/html/

# Set correct permissions (optional)
RUN chown -R www-data:www-data /var/www/html
