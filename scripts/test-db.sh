#!/bin/bash

# Starts the MySQL container the feature tests use and rebuilds its schema from ./migrations.
# The connection values must match the DB_* env entries in phpunit.xml.

set -euo pipefail

cd "$(dirname "$0")/.." # cd to the package root

CONTAINER=laravel-site-utils-mysql

export DB_CONNECTION=mysql
export DB_HOST=127.0.0.1
export DB_PORT=33306
export DB_DATABASE=site_utils_test
export DB_USERNAME=root
export DB_PASSWORD=secret

if docker container inspect "$CONTAINER" > /dev/null 2>&1; then
    docker start "$CONTAINER" > /dev/null
else
    docker run -d --name "$CONTAINER" \
        -e MYSQL_ROOT_PASSWORD="$DB_PASSWORD" \
        -e MYSQL_DATABASE="$DB_DATABASE" \
        -p "$DB_HOST:$DB_PORT:3306" \
        mysql:8.4 > /dev/null
fi

# mysqladmin ping succeeds before the server accepts TCP connections, so poll with a real query.
for _ in $(seq 1 60); do
    if docker exec "$CONTAINER" mysql -uroot -p"$DB_PASSWORD" -e 'SELECT 1' "$DB_DATABASE" > /dev/null 2>&1; then
        break
    fi
    sleep 2
done

# The service provider does not auto-load the package migrations, so point migrate at them directly.
php test-laravel-app/artisan migrate:fresh --force --realpath --path="$PWD/migrations"
