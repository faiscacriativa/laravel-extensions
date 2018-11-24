<?php

/**
 * PHP Version 7.2
 *
 * @category Middlewares
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Middleware/HandleCorsResponse.php
 */

namespace FaiscaCriativa\LaravelExtensions\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Middleware that manipulate CORS responses when requests expects JSON.
 *
 * @category Middlewares
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Middleware/HandleCorsResponse.php
 */
class HandleCorsResponse
{
    /**
     * Handles the incoming request and manipulates CORS responses
     * for JSON requests.
     *
     * @param Request $request The incoming request.
     * @param Closure $next    The next middleware in the stack.
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->expectsJson()) {
            $responseContent = $response->getContent();

            if (strcmp('Origin not allowed', $responseContent) === 0
                || strcmp('Header not allowed', $responseContent) === 0
                || strcmp('Method not allowed', $responseContent) === 0
            ) {
                return response()
                    ->json(
                        [
                            'error'   => true,
                            'message' => $responseContent
                        ],
                        $response->getStatusCode()
                    );
            }
        }

        return $response;
    }
}
