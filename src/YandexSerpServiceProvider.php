<?php

namespace ParsingBy\YandexSerp;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class YandexSerpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'yandexserp');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'yandexserp');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('yandexserp.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/yandexserp'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/yandexserp'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/yandexserp'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);

          $this->app->booted(function () {
                $schedule = app(Schedule::class);
                $schedule->call(function () {
                    (new YandexSerp)->doCreatePagesToParse();
                })->name('YandexSerp_doCreatePagesToParse')->everyMinute()->withoutOverlapping();

                $schedule->call(function () {
                    (new YandexSerp)->doMergePagesResults();
                })->name('YandexSerp_doMergePagesResults')->everyMinute()->withoutOverlapping();

                $schedule->call(function () {
                    (new YandexSerpJobs)->doParsePages();
                })->name('YandexSerpJobs_doParsePages_' . rand(0,1))->everyMinute()->withoutOverlapping();
            });            
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'yandexserp');

        // Register the main class to use with the facade
        $this->app->singleton('yandexserp', function () {
            return new YandexSerp;
        });  
    }
}
