#!/bin/bash
set -e

# Wait for MySQL to be ready
until mysqladmin ping -hmysql --silent; do
    echo 'Waiting for MySQL server...'
    sleep 2
done

# Run the Laravel migrations
php artisan migrate

# Start Apache
