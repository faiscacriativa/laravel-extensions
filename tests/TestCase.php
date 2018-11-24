<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/tests/TestCase.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use FaiscaCriativa\LaravelExtensions\LaravelExtensions;
use FaiscaCriativa\LaravelExtensions\LaravelExtensionsServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Test case for the package.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/tests/TestCase.php
 */
class TestCase extends OrchestraTestCase
{
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
}
