# Use the official PHP base image
FROM php:7.4-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo_mysql bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the source code to the working directory
COPY . /var/www/html

# Install project dependencies
RUN composer install --optimize-autoloader --no-dev

# Set the correct file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod 775 /var/www/html/storage
RUN chmod 775 /var/www/html/storage/logs
RUN find /var/www/html/storage/ -type d -exec chmod 775 {} \;
RUN chmod -R 777 storage

# Generate the Laravel application key
RUN php artisan key:generate
RUN php artisan migrate --force

# Expose the Apache web server port
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]
