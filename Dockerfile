# Use the PHP 8.2 base image with FPM on Alpine Linux
FROM php:8.2-fpm-alpine

# Install Nginx and wget from Alpine package repository
RUN apk add --no-cache nginx wget

# Create a directory for Nginx runtime files
RUN mkdir -p /run/nginx

# Copy Nginx configuration from the local directory to the container
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Install required PHP extensions to interact with MySQL
RUN docker-php-ext-install bcmath pdo pdo_mysql

# Create a directory for the application
RUN mkdir -p /app

# Copy all content from the local directory to the /app directory inside the container
COPY . /app

# Copy the src directory into the /app directory inside the container
COPY ./src /app

# Download Composer and move it to a globally accessible path
RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"

# Enter the /app directory and perform Composer dependency installation (excluding development dependencies)
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

# Change ownership of the /app directory to www-data (Nginx user)
RUN chown -R www-data: /app

# # Run artisan migrate command to set up the database
# RUN cd /app && php artisan migrate

# # Run artisan db:seed command to seed the database
# RUN cd /app && php artisan db:seed

# Execute the startup.sh script when the container starts
CMD sh /app/docker/startup.sh
