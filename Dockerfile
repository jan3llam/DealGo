# Base image
FROM php:8.0

# Install system dependencies
#RUN apt-get update && apt-get install -y \
#    libzip-dev \
#    unzip \
#    && docker-php-ext-install zip pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

RUN chmod -R 777 storage

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install application dependencies
RUN composer install --no-interaction --optimize-autoloader

# Run migrations in the database
RUN php artisan migrate --force

# Set up Apache configuration
RUN a2enmod rewrite

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Expose port
EXPOSE 80

# Run the Apache server
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
