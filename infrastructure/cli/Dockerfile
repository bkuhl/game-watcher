FROM php:7-alpine

# Copy the application files to the container
ADD . /var/www/html

WORKDIR /var/www/html

RUN \
    apk add --update --no-cache \
        # needed for mcrypt
        libmcrypt \

        # needed for composer
        git zip unzip \

    # php extensions the app will need
    && docker-php-ext-install mcrypt mbstring pdo_mysql \

    # install composer
    && php /var/www/html/infrastructure/install_composer.php \

    # give cli the keys
    && chown -R www-data:www-data /var/www/html \

    # redirect logs to stdout
    && ln -sf /dev/stderr /var/www/html/storage/logs/laravel.log \

    # run laravel's scheduled commands as www-data
    && echo "*       *       *       *       *       php /var/www/html/artisan schedule:run" > /etc/crontabs/www-data

USER www-data

# install dependencies
RUN composer install  --no-interaction --optimize-autoloader --prefer-dist

# clean everything needed from building to maintain
# as light of a container as possible
USER root

RUN apk del libmcrypt git zip unzip