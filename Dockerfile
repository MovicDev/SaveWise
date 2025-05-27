# Use the official PHP Apache image
FROM php:8.2-apache

# Install PDO PostgreSQL driver
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html
