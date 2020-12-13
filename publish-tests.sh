#!/bin/bash

cd "$(dirname "$0")" # cd to directory containing this script

DIR=`pwd`

cd test-laravel-app

php artisan site-utils:publish-tests --output-dir $DIR/tests/Feature/Api --output-namespace Antriver\\LaravelSiteUtilsTests\\Feature\\Api
