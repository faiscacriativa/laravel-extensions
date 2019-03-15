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
use FaiscaCriativa\LaravelExtensions\Events\TokenCreating as TokenCreatingEvent;
use FaiscaCriativa\LaravelExtensions\Events\TokenDeleted as TokenDeletedEvent;
use FaiscaCriativa\LaravelExtensions\Http\Middleware\HandleCorsResponse;
use FaiscaCriativa\LaravelExtensions\Listeners\TokenCreating;
use FaiscaCriativa\LaravelExtensions\Listeners\TokenDeleted;
use FaiscaCriativa\LaravelExtensions\Token;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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

        $loader = AliasLoader::getInstance();
        $loader->alias('Agent', \Jenssegers\Agent\Facades\Agent::class);

        Passport::useTokenModel(Token::class);

        Event::listen(TokenCreatingEvent::class, TokenCreating::class);
        Event::listen(TokenDeletedEvent::class, TokenDeleted::class);
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
