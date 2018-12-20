#!/bin/sh
echo "installing default laravel app"
composer global require laravel/installer
composer create-project --prefer-dist laravel/laravel lccTestApp


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
