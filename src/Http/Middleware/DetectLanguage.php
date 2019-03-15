<?php

/**
 * PHP Version 7.2
 *
 * @category Middlewares
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Middleware/DetectLanguage.php
 */

namespace FaiscaCriativa\LaravelExtensions\Http\Middleware;

use Closure;

/**
 * Middleware that detect and set the application language.
 *
 * @category Middlewares
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Middleware/DetectLanguage.php
 */
class DetectLanguage
{
    /**
     * Handles the incoming request and detect the browser
     * preferred language and set it.
     *
     * @param Request $request The incoming request.
     * @param Closure $next    The next middleware in the stack.
     *
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $availableLanguages = ['en', 'pt'];
        $language = $request->getPreferredLanguage($availableLanguages);

        app()->setLocale($language);

        return $next($request);
    }
}
