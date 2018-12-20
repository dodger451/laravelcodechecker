#!/bin/sh
mkdir lccTestProject
cd lccTestProject
echo "installing default laravel app"
echo "-> composer require -n --working-dir=. laravel/installer"
composer require -n --working-dir=. laravel/installer
echo "-> composer create-project --prefer-dist laravel/laravel lccTestApp"
composer create-project --prefer-dist laravel/laravel lccTestApp

echo "require LaravelCodeChecker from repo"
echo "-> composer config -n --working-dir=. repositories.lcc vcs "
composer config -n --working-dir=. repositories.lcc vcs https://github.com/dodger451/laravelcodechecker
echo "->  composer config -n --working-dir=. github-oauth.github.com $1 "
 composer config -n --working-dir=. github-oauth.github.com $1
 echo "-> composer require -n --working-dir=.  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_COMMIT}"
composer require -n --working-dir=.  --prefer-dist --no-interaction dodger451/laravelcodechecker:dev-${TRAVIS_COMMIT}


echo "publishing LaravelCodeChecker standards to config"
cd lccTestApp
php artisan vendor:publish --tag=laravelcodechecker

echo "test cc all"
php artisan cc:all
