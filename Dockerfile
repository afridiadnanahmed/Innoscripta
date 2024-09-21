# Use an official PHP image as the base image
FROM php:8.1-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Set environment variable for Composer to allow running as superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Composer
COPY --from=composer:2.7.2 /usr/bin/composer /usr/bin/composer

# Copy the project into the container
COPY . /var/www/html

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]
