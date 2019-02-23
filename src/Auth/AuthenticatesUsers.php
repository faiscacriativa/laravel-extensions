<?php

/**
 * PHP Version 7.2
 *
 * @category Auth
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Auth/AuthenticatesUsers.php
 *
 * TODO: Add tests for AuthenticatesUsers trait
 */

namespace FaiscaCriativa\LaravelExtensions\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers as ParentAuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Extends the original AuthenticatesUsers trait to be more API-wise.
 * Uses the Laravel Passport package to issue tokens for the API client.
 *
 * @category Auth
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Auth/AuthenticatesUsers.php
 */
trait AuthenticatesUsers
{
    use HandlesOAuthErrors, ParentAuthenticatesUsers;

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($request->wantsJson()) {
            return $this->issueToken($this->convertRequest($request));
        } else if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Converts the request to a Psr7Request.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function convertRequest(Request $request)
    {
        $request->request->add(
            [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT'),
                'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                'username' => $this->getUsernameValue($request),
                'scope' => ''
            ]
        );

        return (new DiactorosFactory)->createRequest($request);
    }

    /**
     * Get the username value for the OAuth Password Client.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return string|null
     */
    protected function getUsernameValue(Request $request)
    {
        return $request->input('email');
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    protected function issueToken(ServerRequestInterface $request)
    {
        return $this->withErrorHandling(
            function () use ($request) {
                $server = app(AuthorizationServer::class);
                $response = $this->convertResponse(
                    $server->respondToAccessTokenRequest($request, new Psr7Response)
                );

                return response()->json(
                    [
                        'error' => false,
                        'data'  => json_decode($response->getContent())
                    ]
                );
            }
        );
    }
}
