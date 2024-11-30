FROM ftechsolutions/php:8.3-apache

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Replace default Apache config with custom configuration
RUN rm /etc/apache2/sites-available/000-default.conf
COPY /docker/sites-available/000-default.conf /etc/apache2/sites-available/

# Add custom PHP configuration
COPY /docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY /docker/xdebug/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set application folder as an environment variable
ENV APP_HOME /var/www/html

# Copy source files to the application directory
COPY . $APP_HOME

# Install Composer and Node dependencies
RUN cd $APP_HOME && composer install --no-interaction && npm install && npm run dev

# Set correct permissions for storage and cache directories
RUN chmod -R 777 $APP_HOME/storage && \
    chmod -R 777 $APP_HOME/bootstrap/cache

# Uncomment below for production use
RUN cd $APP_HOME && \
    php artisan view:clear && \
    php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:clear && \
    php artisan clear-compiled && \
    php artisan optimize

# Set the user to run the application
USER $user
