#!/bin/bash

set -e
set -x

cd "$(dirname "$0")" # cd to directory containing this script

composer install --no-dev --optimize-autoloader
rm -f bootstrap/cache/packages.php
php artisan config:cache
php artisan route:cache
php artisan queue:restart
php artisan view:clear
