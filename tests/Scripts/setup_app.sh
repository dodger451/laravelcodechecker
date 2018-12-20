#!/bin/sh
echo "installing default laravel app"
composer global require laravel/installer
composer create-project --prefer-dist laravel/laravel lccTestApp

echo "require LaravelCodeChecker from repo"
composer config repositories.lcc vcs https://github.com/dodger451/laravelcodechecker
 composer config github-oauth.github.com $1
composer require  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_COMMIT}


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
