FROM php:7.4-apache

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# This Gist file accompanies my article on Medium for creating a PHP, MySQL and Redis development environment 
#   on macOS. This Dockerfile will create an APACHE, PHP 7.2 server that includes the Xdebug, Igbinary and 
#   Redis PHP extensions from PECL. It will also create PHP.ini overrides that will point session management 
#   to the Redis server created in this same article.
#
#   The article can be found here: 
#   https://medium.com/@crmcmullen/php-how-to-run-your-entire-development-environment-in-docker-containers-on-macos-787784e94f9a

# run non-interactive. Suppresses prompts and just accepts defaults automatically.
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# make sure custom log directories exist
RUN mkdir /usr/local/log; \
    mkdir /usr/local/log/apache2; \
    mkdir /usr/local/log/php; \
    chmod -R ug+w /usr/local/log

# create official PHP.ini file
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u ${uid:-1000} -d /home/${user:-app} ${user:-app}
RUN mkdir -p /home/${user:-app}/.composer && \
    chown -R ${user:-app}:${user:-app} /home/${user:-app}

# Add PHP config file to conf.d
RUN { \
        echo 'short_open_tag = On'; \
        echo 'expose_php = Off'; \    
        echo 'error_reporting = E_ALL & ~E_STRICT'; \
        echo 'display_errors = On'; \
        echo 'error_log = /usr/local/log/php/php_errors.log'; \
        echo 'upload_tmp_dir = /tmp/'; \
        echo 'allow_url_fopen = on'; \
    } > /usr/local/etc/php/conf.d/php-config.ini

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /usr/local/log/apache2

# Configure apache mods
RUN a2enmod rewrite 

# Add ServerName parameter
RUN echo "ServerName localhost" | tee /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

# Update the default apache site with the config we created.
RUN { \
        echo '<VirtualHost *:80>'; \
        echo '    ServerAdmin your_email@example.com'; \
        echo '    DocumentRoot /var/www/html'; \
        echo '    <Directory /var/www/html/>'; \
        echo '        Options Indexes FollowSymLinks MultiViews'; \
        echo '        AllowOverride All'; \
        echo '        Order deny,allow'; \
        echo '        Allow from all'; \
        echo '    </Directory>'; \
        echo '    ErrorLog /usr/local/log/apache2/error.log'; \
        echo '    CustomLog /usr/local/log/apache2/access.log combined' ; \
        echo '</VirtualHost>'; \
    } > /etc/apache2/sites-enabled/000-default.conf

# Set working directory
WORKDIR /var/www

USER $user

# Create app directory
RUN mkdir -p /var/www/src
RUN mkdir -p /var/www/config
RUN mkdir -p /var/www/vendor
RUN mkdir -p /var/www/temp

COPY ./src /var/www/src
COPY ./config.docker /var/www/config
COPY ./composer.json /var/www/
COPY ./composer.lock /var/www/

RUN composer install

COPY ./public.docker /var/www/html/

EXPOSE 80