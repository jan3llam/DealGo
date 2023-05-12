# Use the official PHP image
FROM php:7.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache nginx supervisor

# Copy the application code into the container
COPY . /var/www/html

# Install dependencies using Composer
RUN cd /var/www/html && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --no-scripts --no-autoloader && \
    composer dump-autoload --optimize

# Set the working directory
WORKDIR /var/www/html

# Copy the Nginx and Supervisor configuration files into the container
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Start Supervisor to run Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
