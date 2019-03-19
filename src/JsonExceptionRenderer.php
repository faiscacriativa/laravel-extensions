<?php

/**
 * PHP Version 7.2
 *
 * @category Renderers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/JsonExceptionRenderer.php
 *
 * @todo Change category to Traits?
 * @todo Move to a "Traits" folder?
 * @todo Add tests to the handleAuthenticationException and handleException methods.
 */

namespace FaiscaCriativa\LaravelExtensions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Renderers some exceptions to a JSON response.
 *
 * @category Renderers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/JsonExceptionRenderer.php
 */
trait JsonExceptionRenderer
{
    /**
     * Process the exception as a JSON formatted response.
     *
     * @param Exception $exception The triggered exception.
     *
     * @return void
     */
    public function renderJsonResponse(Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return $this->handleAuthenticationException($exception);
        }

        if ($exception instanceof InvalidSignatureException) {
            return $this->handleInvalidSignatureException($exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->handleNotFoundException($exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        }

        return $this->handleException($exception);
    }

    /**
     * Handles the authentication exception.
     *
     * @param AuthenticationException $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleAuthenticationException(
        AuthenticationException $exception
    ) {
        return response()->json(
            [
                'error' => true,
                'message' => $exception->getMessage()
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Handles any exception.
     *
     * @param Exception $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleException(Exception $exception)
    {
        $responseBody = [
            'error' => true,
            'message' => trans('errors.generic_error_message')
        ];

        if (!App::environment('production')) {
            $responseBody['message'] = $exception->getMessage();
            $responseBody['data'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stacktrace' => $exception->getTrace()
            ];
        }

        return response()->json(
            $responseBody,
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Handles Invalid signature exceptions.
     *
     * @param InvalidSignatureException $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleInvalidSignatureException(
        InvalidSignatureException $exception
    ) {
        return response()->json(
            [
                'error'   => true,
                'message' => trans('routing.invalid_signature')
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Handles the "Model not found" exception.
     *
     * @param ModelNotFoundException $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleModelNotFoundException(
        ModelNotFoundException $exception
    ) {
        return response()
            ->json(
                [
                    'error' => true,
                    'message' => trans('errors.not_found')
                ],
                Response::HTTP_NOT_FOUND
            );
    }

    /**
     * Handle the "Not found" exception.
     *
     * @param NotFoundException $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleNotFoundException(NotFoundHttpException $exception)
    {
        return response()
            ->json(
                [
                    'error' => true,
                    'message' => trans('errors.invalid_endpoint')
                ],
                Response::HTTP_NOT_FOUND
            );
    }

    /**
     * Handles the validation exception.
     *
     * @param ValidationException $exception The triggered exception.
     *
     * @return Response
     */
    protected function handleValidationException(ValidationException $exception)
    {
        $errors    = [];
        $errorsBag = $exception->errors();

        foreach ($errorsBag as $field => $error) {
            $errorMessage = $error;

            if (is_array($error) and count($error) == 1) {
                $errorMessage = $error[0];
            }

            array_push($errors, ['field' => $field, 'message' => $errorMessage]);
        }

        return response()
            ->json(
                [
                    'error'   => true,
                    'message' => trans('validation.verify_prompt'),
                    'data'    => $errors
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
    }
}
