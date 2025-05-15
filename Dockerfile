# Use the official PHP image with Apache
FROM php:8.2-apache

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

# Install Node.js (for Laravel Mix/Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy Laravel app files
COPY . .

# Copy Apache configuration to serve Laravel from /public
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Point Apache root to Laravel's public folder
RUN rm -rf /var/www/html && ln -s /var/www/public /var/www/html

# Install Composer (copy from official composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set correct permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 storage bootstrap/cache

# Set Composer memory limit
ENV COMPOSER_MEMORY_LIMIT=-1

# Set Git safe directory to avoid "dubious ownership" error
RUN git config --global --add safe.directory /var/www


# Install PHP and JavaScript dependencies
RUN composer install --no-interaction --optimize-autoloader
RUN npm install && npm run build


# Expose Apache port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
