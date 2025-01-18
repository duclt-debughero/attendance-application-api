FROM php:8.3-apache

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install packages and enable modules
RUN apt-get update \
    && apt-get install -y git unzip zip git npm exiftool \
    && apt-get install -y vim \
    && a2enmod ssl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache exif mbstring

# Replace default Apache config with custom configuration
RUN rm /etc/apache2/sites-available/000-default.conf
COPY /docker/sites-available/000-default.conf /etc/apache2/sites-available/

# Enable Apache rewrite module
RUN a2enmod rewrite

# Add custom PHP configuration
COPY /docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer
RUN chown -R $user:$user /home/$user

# Set application folder as an environment variable
ENV APP_HOME=/var/www/html

# Copy source files to the application directory
COPY . $APP_HOME

# Install Composer and Node dependencies
RUN cd $APP_HOME && composer install --no-interaction && npm install && npm run prod

# Set correct permissions for storage and cache directories
RUN chmod -R 777 $APP_HOME/storage
RUN chmod -R 777 $APP_HOME/bootstrap/cache

# Uncomment below for production use
RUN cd $APP_HOME && php artisan view:clear

# Set the user to run the application
USER root
