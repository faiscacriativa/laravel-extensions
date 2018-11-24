<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsPreflightTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/PreflightTest.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Illuminate\Http\Response;

/**
 * Test CORS preflight requests.
 * Fork from Barryvdh\Cors\Tests\PreflightTest.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CorsPreflightTest.php
 * @see      https://github.com/barryvdh/laravel-cors/blob/master/tests/PreflightTest.php
 */
class CorsPreflightTest extends TestCase
{
    /**
     * Test request with allowed origin.
     *
     * @return void
     */
    public function testAllowOriginAllowed()
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
     * Test request with wildcard allowed origin.
     *
     * @return void
     */
    public function testAllowWildcardOriginAllowed()
    {
        config(['cors.allowedOrigins' => ['*.laravel.com']]);

        $crawler = $this->call(
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'blog.laravel.com',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            ]
        );
        $this->assertEquals(
            'blog.laravel.com',
            $crawler->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with not allowed origin.
     *
     * @return void
     */
    public function testAllowOriginNotAllowed()
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
     * Test request with not found route.
     *
     * @return void
     */
    public function testAllowNotFoundUsesConfig()
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
            'GET, POST',
            $crawler->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with allowed method.
     *
     * @return void
     */
    public function testAllowMethodAllowed()
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
            'GET, POST',
            $crawler->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with not allowed method.
     *
     * @return void
     */
    public function testAllowMethodNotAllowed()
    {
        $crawler = $this->call(
            'OPTIONS',
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
        $this->assertEquals(405, $crawler->getStatusCode());
    }

    /**
     * Test request with allowed methods (for web routes).
     *
     * @return void
     */
    public function testAllowMethodsForWeb()
    {
        $crawler = $this->call(
            'OPTIONS',
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
            'GET, POST',
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
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'X-Custom-1, X-Custom-2',
            ]
        );
        $this->assertEquals(
            'x-custom-1, x-custom-2',
            $crawler->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with all headers allowed.
     *
     * @return void
     */
    public function testAllowAllHeaderAllowed()
    {
        config(['cors.allowedHeaders' => ['*']]);

        $crawler = $this->call(
            'OPTIONS',
            'api/ping',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'localhost',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'X-Custom-3',
            ]
        );
        $this->assertEquals(
            'X-CUSTOM-3',
            $crawler->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /**
     * Test request with not allowed header.
     *
     * @return void
     */
    public function testAllowHeaderNotAllowed()
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
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'x-custom-3',
            ]
        );
        $this->assertEquals(
            null,
            $crawler->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(403, $crawler->getStatusCode());
    }
}
