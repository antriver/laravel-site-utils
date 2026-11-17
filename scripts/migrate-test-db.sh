#!/bin/bash

# Rebuilds the test database schema from ./migrations.
# The connection defaults match the DB_* env entries in phpunit.xml, and any of them can be overridden.

set -euo pipefail

cd "$(dirname "$0")/.." # cd to the package root

export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-127.0.0.1}"
export DB_PORT="${DB_PORT:-33306}"
export DB_DATABASE="${DB_DATABASE:-site_utils_test}"
export DB_USERNAME="${DB_USERNAME:-root}"
export DB_PASSWORD="${DB_PASSWORD:-secret}"

# The service provider does not auto-load the package migrations, so point migrate at them directly.
php test-laravel-app/artisan migrate:fresh --force --realpath --path="$PWD/migrations"
