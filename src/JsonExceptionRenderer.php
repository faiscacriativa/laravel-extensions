<?php

/**
 * PHP Version 7.2
 *
 * @category Renderers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/JsonExceptionRenderer.php
 */

namespace FaiscaCriativa\LaravelExtensions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Renderers some exceptions to a JSON response.
 *
 * @category Renderers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/tree/master/src/JsonExceptionRenderer.php
 */
trait JsonExceptionRenderer
{
    /**
     * Processa a exceção como uma resposta no formato JSON.
     *
     * @param Exception $exception A exceção disparada.
     *
     * @return void
     */
    public function renderJsonResponse(Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return $this->handleNotFoundException($exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        }
    }

    /**
     * Lida com a exceção de rota não encontrada.
     *
     * @param NotFoundException $exception A exceção disparada.
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
     * Lida com a exceção de "modelo não encontrado".
     *
     * @param ModelNotFoundException $exception A exceção disparada.
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
     * Lida com a exceção de validação.
     *
     * @param ValidationException $exception A exceção disparada.
     *
     * @return Response
     */
    protected function handleValidationException(ValidationException $exception)
    {
        return response()
            ->json(
                [
                    'error'   => true,
                    'message' => trans('validation.verifyPrompt'),
                    'data'    => $exception->errors()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
    }
}
