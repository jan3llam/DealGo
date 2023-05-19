# Use an official PHP runtime as the base image
FROM php:8.0-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the composer files and install dependencies
COPY ../composer.json ../composer.lock ./
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copy the rest of the application source code
COPY . .

# Set the ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Set the environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV APACHE_LOG_DIR /var/log/apache2

# Enable Apache modules
RUN a2enmod rewrite

# Copy the virtual host configuration
COPY ./docker/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install MySQL client
RUN apt-get install -y default-mysql-client

# Set the entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
