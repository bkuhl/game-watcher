FROM bkuhl/php:7.1

WORKDIR /var/www/html

RUN apk add --update --no-cache \

        # clone via ssh
        openssh

# add the application to the container
ADD . /var/www/html

# Can be removed once https://github.com/moby/moby/issues/6119 is released
RUN chown -R www-data:www-data /var/www/html /home/www-data

# Run composer as www-data
# Can be moved before application files are added to the container once
# the issue mentioned above is fixed and released
USER www-data

# temp directory so we can download game clients to generate their sha1
RUN chmod 775 storage/app \

    # redirect logs to stdout
    && ln -sf /dev/stderr storage/logs/laravel.log \

    && chmod +x infrastructure/start.sh \

    # production-ready dependencies
    && composer install  --no-interaction --optimize-autoloader --no-dev --prefer-dist \

    # keep the container light weight
    && rm -rf /home/www-data/.composer/cache

# Setup ssh/git configuration and run cron as the main process
USER root

CMD infrastructure/start.sh;crond -f -d 8