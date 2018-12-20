#!/bin/sh
mkdir lccTestProject
cd lccTestProject
composer init -n
echo "installing default laravel app"
echo "-> composer require -n laravel/installer"
composer require -n laravel/installer
echo "-> composer create-project --prefer-dist laravel/laravel lccTestApp"
composer create-project --prefer-dist laravel/laravel lccTestApp

echo "require LaravelCodeChecker from repo"
echo "-> composer config -n repositories.lcc vcs "
composer config -n repositories.lcc vcs https://github.com/dodger451/laravelcodechecker
echo "->  composer config -n github-oauth.github.com $1 "
 composer config -n github-oauth.github.com $1
 echo "-> composer require -n  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_BRANCH}#${TRAVIS_COMMIT}"
composer require -n  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_BRANCH}#${TRAVIS_COMMIT}


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
