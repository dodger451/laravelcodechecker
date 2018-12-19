#!/bin/sh
echo "installing default laravel app"
composer global require laravel/installer
composer create-project --prefer-dist laravel/laravel lccTestApp

echo "require LaravelCodeChecker from repo"
composer config repositories.lcc vcs https://github.com/dodger451/LaravelCodeChecker
composer require dodger451/LaravelCodeChecker:dev-master
composer update dodger451/LaravelCodeChecker


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
