<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsGroupMiddlewareTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/GroupMiddlewareTest.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Illuminate\Http\Response;

/**
 * Test CORS middleware in a route group.
 * Fork from Barryvdh\Cors\Tests\GroupMiddlewareTest.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsGroupMiddlewareTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/GroupMiddlewareTest.php
 */
class CorsGroupMiddlewareTest extends TestCase
{
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
     * Test request with allowed origin.
     *
     * @return void
     */
    public function testAllowOriginAllowed()
    {
        $crawler = $this->call(
            'POST',
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
     * Test request with allowed method.
     *
     * @return void
     */
    public function testAllowMethodAllowed()
    {
        $crawler = $this->call(
            'POST',
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
            null,
            $crawler->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(200, $crawler->getStatusCode());

        $this->assertEquals('PONG', $crawler->getContent());
    }

    /**
     * Test request with not allowed method.
     *
     * @return void
     */
    public function testAllowMethodNotAllowed()
    {
        $crawler = $this->call(
            'POST',
            'api/ping',
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
     * Test request with not allowe method (for web).
     *
     * @return void
     */
    public function testAllowMethodsForWebNotAllowed()
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
            'api/ping',
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
            'api/ping',
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
            'api/error',
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
            'api/validation',
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
