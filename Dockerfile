FROM php:8.1-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    libpq-dev libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd mbstring bcmath intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Copy Laravel files
COPY . /var/www

# Fix Apache root to serve from Laravel public/
RUN rm -rf /var/www/html \
    && ln -s /var/www/public /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Set Composer memory limit
ENV COMPOSER_MEMORY_LIMIT=-1

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs --verbose

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
