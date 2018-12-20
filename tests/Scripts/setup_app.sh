#!/bin/sh
mkdir lccTestProject
cd lccTestProject
echo "installing default laravel app"
echo "-> composer -n -d . require laravel/installer"
composer -n -d . require laravel/installer
echo "-> composer create-project --prefer-dist laravel/laravel lccTestApp"
composer create-project --prefer-dist laravel/laravel lccTestApp

echo "require LaravelCodeChecker from repo"
echo "-> composer -n -d config repositories.lcc vcs "
composer -n -d config repositories.lcc vcs https://github.com/dodger451/laravelcodechecker
echo "->  composer -n -d config github-oauth.github.com $1 "
 composer -n -d config github-oauth.github.com $1
 echo "-> composer -n -d . require  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_COMMIT}"
composer -n -d . require  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_COMMIT}


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
