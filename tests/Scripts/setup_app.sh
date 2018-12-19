#!/bin/sh
composer global require laravel/installer
composer create-project --prefer-dist laravel/laravel lccTestApp
composer config repositories.lcc vcs https://github.com/dodger451/LaravelCodeChecker
composer require dodger451/LaravelCodeChecker:dev-master

cd lccTestApp

php artisan vendor:publish --tag=laravelcodechecker
