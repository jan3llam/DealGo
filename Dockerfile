# Base image
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

RUN chmod 775 /var/www/html/storage
RUN chmod 775 /var/www/html/storage/logs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install application dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set up Apache configuration
RUN a2enmod rewrite

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Expose port
EXPOSE 80

# Run the Apache server
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
