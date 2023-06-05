# Use the official PHP base image
FROM php:7.4-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    libzip-dev \
    unzip

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo_mysql bcmath zip pdo pdo_mysql


# Copy the source code to the working directory
COPY . /var/www/html

# Set the correct file permissions

RUN chown -R www-data:www-data /var/www/html/public /var/www/html/public/images
RUN chmod 775 /var/www/html/public
RUN chmod 775 /var/www/html/public/images
RUN find /var/www/html/public/ -type d -exec chmod 775 {} \;

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod 775 /var/www/html/storage
RUN chmod 775 /var/www/html/storage/logs
RUN find /var/www/html/storage/ -type d -exec chmod 775 {} \;
RUN chmod -R 777 storage

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install --optimize-autoloader --no-dev --ignore-platform-req=php

# Generate the Laravel application key
RUN php artisan key:generate
RUN php artisan migrate --force

# Set up Apache configuration
RUN a2enmod rewrite

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Expose the Apache web server port
EXPOSE 80

# Run the Apache server
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]

# Start the Apache web server
# CMD ["apache2-foreground"]
