# Run on local machine

## Prerequistes 

Have PHP version `8.2`

You must install PHP composer

https://getcomposer.org/download/

Check by running `composer --version`

## Installation 

Run `composer update`

Run `composer install`

Run `php artisan key:generate` 


# Run locally with Docker

## Prerequistes 
First you must install Docker on your machine.

https://www.docker.com/products/docker-desktop/

You must launch the Docker desktop first

## Build Docker Image
Run `docker build .`

This will generate a unique image id of `sha:<id>`

# Run Docker Image
Run `docker run -p 80:80 <image_id>`

This will map the Docker image's port `80` to your local port `80`

So, you can access the web app with `localhost` on port `80`
