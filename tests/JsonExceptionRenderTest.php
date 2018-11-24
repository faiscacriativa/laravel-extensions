<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/tests/JsonExceptionRenderTest.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Exception;
use FaiscaCriativa\LaravelExtensions\JsonExceptionRenderer;
use Illuminate\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Test the JsonExceptionRender trait functionality.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/tests/JsonExceptionRenderTest.php
 */
class JsonExceptionRenderTest extends TestCase
{
    /**
     * The service container service.
     *
     * @var Container
     */
    protected $container;

    /**
     * The exception handler to test the trait.
     *
     * @var ExceptionHandlerTestClass
     */
    protected $handler;

    /**
     * The mock object for a request.
     *
     * @var stdClass
     */
    protected $request;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->container = Container::getInstance();
        $this->handler   = new ExceptionHandlerTestClass($this->container);
        $this->request   = Mockery::mock(stdClass::class);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Test the ModelNotFoundException transformation.
     *
     * @return void
     */
    public function testModelNotFoundException()
    {
        $this->request->shouldReceive('expectsJson')->once()->andReturnTrue();

        $response = $this->handler
            ->render($this->request, new ModelNotFoundException);

        $content = $response->getContent();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotContains('<!DOCTYPE html>', $content);
        $this->assertContains('"error":true', $content);
        $this->assertContains(
            sprintf('"message":"%s"', trans('errors.not_found')),
            $content
        );
    }

    /**
     * Test the NotFoundHttpException transformation.
     *
     * @return void
     */
    public function testNotFoundHttpException()
    {
        $this->request->shouldReceive('expectsJson')->once()->andReturnTrue();

        $response = $this->handler
            ->render($this->request, new NotFoundHttpException);

        $content = $response->getContent();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotContains('<!DOCTYPE html>', $content);
        $this->assertContains('"error":true', $content);
        $this->assertContains(
            sprintf('"message":"%s"', trans('errors.invalid_endpoint')),
            $content
        );
    }

    /**
     * Test the ValidationException transformation.
     *
     * @return void
     */
    public function testValidationException()
    {
        $this->request->shouldReceive('expectsJson')->once()->andReturnTrue();

        $validator = Validator::make(['required' => ''], ['required' => 'required']);

        $response = $this->handler
            ->render($this->request, new ValidationException($validator));

        $content = $response->getContent();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertNotContains('<!DOCTYPE html>', $content);
        $this->assertContains('"error":true', $content);
        $this->assertContains(
            sprintf('"message":"%s"', trans('validation.verifyPrompt')),
            $content
        );
        $this->assertContains('"data":{"required":', $content);
    }
}

/**
 * The exception handler test class.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/tests/JsonExceptionRenderTest.php
 */
class ExceptionHandlerTestClass extends ExceptionHandler
{
    use JsonExceptionRenderer;

    /**
     * Transform an exception in a HTTP response.
     *
     * @param Request   $request   The incoming request.
     * @param Exception $exception The triggered exception.
     *
     * @return Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return $this->renderJsonResponse($exception);
        }

        return parent::render($request, $exception);
    }
}
