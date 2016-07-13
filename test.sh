#!/usr/bin/env bash

composer install --no-interaction --prefer-dist
vendor/bin/phpcs --config-set ignore_warnings_on_exit 1
vendor/bin/phpcs --standard=PSR2 --ignore=app/Http/routes.php app
vendor/bin/phpunit --testsuite=unit