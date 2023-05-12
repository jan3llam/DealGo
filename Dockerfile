# base image
FROM php:7.4-fpm

# update and install required packages
RUN apt-get update && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# copy source code to container
COPY . /var/www/html

# install dependencies
WORKDIR /var/www/html
RUN composer install --no-interaction --no-dev --prefer-dist

# copy configuration files
COPY .env.example .env
RUN php artisan key:generate

# set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# expose port
EXPOSE 9000

# start the server
CMD ["php-fpm"]
