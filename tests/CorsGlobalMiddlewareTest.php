<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsGlobalMiddlewareTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/GlobalMiddlewareTest.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

/**
 * Test CORS middleware as global middleware.
 * Fork from Barryvdh\Cors\Tests\GlobalMiddlewareTest.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsGlobalMiddlewareTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/GlobalMiddlewareTest.php
 */
class CorsGlobalMiddlewareTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param Application $app The instance of application.
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Add the middleware
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->prependMiddleware(\Barryvdh\Cors\HandleCors::class);

        parent::getEnvironmentSetUp($app);
    }

    /**
     * Test request without origin.
     *
     * @return void
     */
    public function testShouldReturnNullOnHeaderAssessControlAllowOriginBecauseDontHaveHttpOriginOnRequest()
    {
        $crawler = $this->call(
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            ['HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST']
        );

        $this->assertNull($crawler->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test OPTIONS request with allowed origin.
     *
     * @return void
     */
    public function testOptionsAllowOriginAllowed()
    {
        $crawler = $this->call(
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            'localhost',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test OPTIONS request with allowed origin, but not existing route.
     *
     * @return void
     */
    public function testOptionsAllowOriginAllowedNonExistingRoute()
    {
        $crawler = $this->call(
            'OPTIONS',
            'api/pang',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            'localhost',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test OPTIONS request with not allowed origin.
     *
     * @return void
     */
    public function testOptionsAllowOriginNotAllowed()
    {
        $crawler = $this->call(
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'otherhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(403, $crawler->getStatusCode());
    }

    /**
     * Test request with allowed origin.
     *
     * @return void
     */
    public function testAllowOriginAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            'localhost',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(200, $crawler->getStatusCode());

        $this->assertEquals('PONG', $crawler->getContent());
    }

    /**
     * Test request with not allowed origin.
     *
     * @return void
     */
    public function testAllowOriginNotAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'otherhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(403, $crawler->getStatusCode());
    }

    /**
     * Test request with allowed method.
     *
     * @return void
     */
    public function testAllowMethodAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );
        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(200, $crawler->getStatusCode());

        $this->assertEquals('PONG', $crawler->getContent());
    }

    /**
     * Test request with method not allowed.
     *
     * @return void
     */
    public function testAllowMethodNotAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'PUT',
            ]
        );
        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with allowed header.
     *
     * @return void
     */
    public function testAllowHeaderAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'x-custom-1, x-custom-2',
            ]
        );
        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(200, $crawler->getStatusCode());

        $this->assertEquals('PONG', $crawler->getContent());
    }

    /**
     * Test request with not allowed header.
     *
     * @return void
     */
    public function testAllowHeaderNotAllowed()
    {
        $crawler = $this->call(
            'POST',
            'web/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'x-custom-3',
            ]
        );
        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with error.
     *
     * @return void
     */
    public function testError()
    {
        if ($this->checkVersion('5.3', '<')) {
            $this->markTestSkipped(
                'Catching exceptions is not possible on Laravel 5.1'
            );
        }

        $crawler = $this->call(
            'POST',
            'web/error',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );

        $this->assertEquals(
            'localhost',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(500, $crawler->getStatusCode());
    }

    /**
     * Test request with validation.
     *
     * @return void
     */
    public function testValidationException()
    {
        if ($this->checkVersion('5.3', '<')) {
            $this->markTestSkipped(
                'Catching exceptions is not possible on Laravel 5.1'
            );
        }

        $crawler = $this->call(
            'POST',
            'web/validation',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );
        $this->assertEquals(
            'localhost',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(302, $crawler->getStatusCode());
    }
}
