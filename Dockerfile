# Use a valid PHP + Apache image
FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Copy Laravel project files into the container
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
