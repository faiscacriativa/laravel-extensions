<?php

/**
 * PHP Version 7.2
 *
 * @category ServiceProviders
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/LaravelExtensionsServiceProvider.php
 */

namespace FaiscaCriativa\LaravelExtensions;

use Barryvdh\Cors\HandlePreflight;
use FaiscaCriativa\LaravelExtensions\Http\Middleware\HandleCorsResponse;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel Extensions service provider.
 *
 * @category ServiceProviders
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/LaravelExtensionsServiceProvider.php
 */
class LaravelExtensionsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $kernel = $this->app->make(Kernel::class);

        if ($kernel->hasMiddleware(HandlePreflight::class)) {
            $kernel->prependMiddleware(HandleCorsResponse::class);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Barryvdh\Cors\ServiceProvider::class);
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the CORS configuration file
        $this->publishes(
            [__DIR__ . '/../config/cors.php' => config_path('cors.php')]
        );
    }
}
