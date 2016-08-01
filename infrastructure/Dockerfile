FROM php:7-alpine

RUN apk add --update --no-cache \
        # needed for mcrypt
        libmcrypt \

        # needed for composer
        git zip unzip

# php extensions the app will need
RUN docker-php-ext-install mcrypt mbstring pdo_mysql

# add the application to the container
ADD . /var/www/html

WORKDIR /var/www/html

# temp directory so we can download game clients to generate their sha1
RUN chmod 775 /var/www/html/storage/app

# install composer
RUN wget -O /usr/local/bin/composer http://getcomposer.org/composer.phar \
    && chmod +x /usr/local/bin/composer \

    # give cli the keys
    && chown -R www-data:www-data /var/www/html /usr/local/bin/composer \

    && mkdir tmp

# redirect logs to stdout
RUN ln -sf /dev/stderr /var/www/html/storage/logs/laravel.log

# run laravel's scheduled commands as www-data
RUN echo "*       *       *       *       *       php /var/www/html/artisan schedule:run" > /etc/crontabs/www-data

USER www-data

# install dependencies
RUN composer install --no-dev --no-interaction --prefer-dist

# clean everything needed from building to maintain
# as light of a container as possible
USER root

RUN apk del libmcrypt zip unzip

CMD ["/var/www/html/infrastructure/start.sh;crond -f -d 8"]