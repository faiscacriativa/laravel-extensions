<?php

namespace FaiscaCriativa\LaravelExtensions;

use Illuminate\Support\ServiceProvider;

class LaravelExtensionsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'faiscacriativa');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'faiscacriativa');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelextensions.php', 'laravelextensions');

        // Register the service the package provides.
        $this->app->singleton('laravelextensions', function ($app) {
            return new LaravelExtensions;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelextensions'];
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
            __DIR__.'/../config/laravelextensions.php' => config_path('laravelextensions.php'),
        ], 'laravelextensions.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/faiscacriativa'),
        ], 'laravelextensions.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/faiscacriativa'),
        ], 'laravelextensions.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/faiscacriativa'),
        ], 'laravelextensions.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
