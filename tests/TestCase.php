<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/TestCase.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Barryvdh\Cors\HandleCors;
use FaiscaCriativa\LaravelExtensions\LaravelExtensions;
use FaiscaCriativa\LaravelExtensions\LaravelExtensionsServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Test case for the package.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/TestCase.php
 */
class TestCase extends OrchestraTestCase
{
    use ValidatesRequests;

    /**
     * Get the environment setup.
     *
     * @param Application $app The application instance.
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $router = $app['router'];

        $this->addWebRoutes($router);
        $this->addApiRoutes($router);
    }

    /**
     * Get the package aliases.
     *
     * @param Application $app The application instance.
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [LaravelExtensions::class];
    }

    /**
     * Get package service providers.
     *
     * @param Application $app The application instance.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelExtensionsServiceProvider::class];
    }

    /**
     * Adds the fake API routes .
     * Fork from Barryvdh\Cors\Tests\TestCase.
     *
     * @param Router $router The router instance.
     *
     * @return void
     * @see    https://github.com/barryvdh/laravel-cors/blob/master/tests/TestCase.php
     */
    protected function addApiRoutes($router)
    {
        $router->middleware(['middleware' => HandleCors::class])
            ->group(
                function () use ($router) {
                    $router->get(
                        'api/ping',
                        function () {
                            return 'pong';
                        }
                    )->name('api.ping');

                    $router->post(
                        'api/ping',
                        function () {
                            return 'PONG';
                        }
                    );

                    $router->put(
                        'api/ping',
                        function () {
                            return 'PONG';
                        }
                    );

                    $router->post(
                        'api/error',
                        function () {
                            abort(500);
                        }
                    );

                    $router->post(
                        'api/validation',
                        function (Request $request) {
                            $this->validate(
                                $request,
                                ['name' => 'required']
                            );

                            return 'ok';
                        }
                    );
                }
            );
    }

    /**
     * Adds the fake web routes.
     * Fork from Barryvdh\Cors\Tests\TestCase.
     *
     * @param Router $router The router instance.
     *
     * @return void
     * @see    https://github.com/barryvdh/laravel-cors/blob/master/tests/TestCase.php
     */
    protected function addWebRoutes(Router $router)
    {
        $router->get(
            'web/ping',
            function () {
                return 'pong';
            }
        )->name('web.ping');

        $router->post(
            'web/ping',
            function () {
                return 'PONG';
            }
        );

        $router->post(
            'web/error',
            function () {
                abort(500);
            }
        );

        $router->post(
            'web/validation',
            function (Request $request) {
                $this->validate(
                    $request,
                    ['name' => 'required']
                );

                return 'ok';
            }
        );
    }

    /**
     * Check Laravel version with the informed one.
     * Fork from Barryvdh\Cors\Tests\TestCase.
     *
     * @param string $version  The version to be checked.
     * @param string $operator The operator to be used in the comparison.
     *
     * @return bool
     * @see    https://github.com/barryvdh/laravel-cors/blob/master/tests/TestCase.php
     */
    protected function checkVersion($version, $operator = ">=")
    {
        return version_compare($this->app->version(), $version, $operator);
    }

    /**
     * Resolves the application configuration.
     *
     * @param Application $app The application instance.
     *
     * @return void
     */
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['cors'] = [
            'supportsCredentials' => false,
            'allowedOrigins' => ['localhost'],
            'allowedHeaders' => ['X-Custom-1', 'X-Custom-2'],
            'allowedMethods' => ['GET', 'POST'],
            'exposedHeaders' => [],
            'maxAge' => 0,
        ];
    }
}
