<?php

namespace dodger451\LaravelCodeChecker;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class LaravelCodeCheckerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dodger451');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        parent::boot();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelcodechecker.php', 'laravelcodechecker');

        // Register the service the package provides.
        $this->app->singleton('laravelcodechecker', function () {
            return new LaravelCodeChecker;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelcodechecker'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelcodechecker.php' => config_path('laravelcodechecker.php'),
        ], 'laravelcodechecker');

        $this->publishes([
            __DIR__.'/Templates/' => config_path(''),
        ], 'laravelcodechecker');
    }

    /**
     * Define the commands for the application.
     *
     * @return void
     */
    public function map()
    {
        \Artisan::command('cc:phpcs {targets?*}', function ($targets) {
            app('laravelcodechecker')->phpcsCheck($targets);
        })
            ->describe('laravelcodechecker: Check formatting errors with PHPCS');

        \Artisan::command('cc:phpcs:fix {targets?*}', function ($targets) {
            app('laravelcodechecker')->phpcsFix($targets);
        })
            ->describe('laravelcodechecker: Fix formatting errors with PHPCBF');

        \Artisan::command('cc:phplint {targets?*}', function ($targets) {
            app('laravelcodechecker')->phpLint($targets);
        })
            ->describe('laravelcodechecker: Find syntax errors with php -l on all files');

        \Artisan::command('cc:phpmd {targets?*}', function ($targets) {
            app('laravelcodechecker')->phpmd($targets);
        })
            ->describe('laravelcodechecker: Find messy code with phpmd ');

        \Artisan::command('cc:all {targets?*}', function ($targets) {
            app('laravelcodechecker')->phpLint($targets);
            app('laravelcodechecker')->phpcsCheck($targets);
            app('laravelcodechecker')->phpmd($targets);
        })
            ->describe('laravelcodechecker: Run phplint, phpcs, phpmd ');
    }
}
