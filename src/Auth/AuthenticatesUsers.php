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

use FaiscaCriativa\LaravelExtensions\Token;
use Illuminate\Foundation\Auth\AuthenticatesUsers as ParentAuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
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
     * Handles the login request to the application.
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
            return $this->issueToken($this->convertPasswordGrantRequest($request));
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
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($request->wantsJson()) {
            return $this->revokeToken($request);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * Handles the token refresh request.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        return $this->issueToken($this->convertRefreshGrantRequest($request));
    }

    /**
     * Converts the request to a Psr7Request for password token.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function convertPasswordGrantRequest(Request $request)
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
     * Converts the request to a Psr7Request for access token refresh.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function convertRefreshGrantRequest(Request $request)
    {
        $request->request->add(
            [
                'grant_type' => 'refresh_token',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT'),
                'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                'refresh_token' => $this->getRefreshTokenValue($request),
                'scope' => ''
            ]
        );

        return (new DiactorosFactory)->createRequest($request);
    }

    /**
     * Get the refresh token value for the OAuth Password Client.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return string|null
     */
    protected function getRefreshTokenValue(Request $request)
    {
        return $request->input('refresh_token');
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

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        if (request()->wantsJson()) {
            return Auth::guard('api');
        }

        return Auth::guard();
    }

    /**
     * Revokes the token from the user.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    protected function revokeToken(Request $request)
    {
        $jti = $request->input('jti');

        $token = Token::find($jti);

        if (!empty($token)) {
            $token->revoke();
            $token->delete();
        }

        return response()->json(
            [
                'error'    => false,
                'message'  => 'Ok'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Perform the given callback with exception handling.
     *
     * @param  \Closure  $callback
     * @return \Illuminate\Http\Response
     */
    protected function withErrorHandling($callback)
    {
        try {
            return $callback();
        } catch (OAuthServerException $e) {
            $this->exceptionHandler()->report($e);

            $response = $this->convertResponse(
                $e->generateHttpResponse(new Psr7Response)
            );

            $responseBody = json_decode($response->getContent(), true);
            $responseBody['message'] = Lang::getFromJson($responseBody['message']);

            $response->setContent(json_encode($responseBody));

            return $response;
        } catch (Exception $e) {
            $this->exceptionHandler()->report($e);

            return new Response($this->configuration()->get('app.debug') ? $e->getMessage() : 'Error.', 500);
        } catch (Throwable $e) {
            $this->exceptionHandler()->report(new FatalThrowableError($e));

            return new Response($this->configuration()->get('app.debug') ? $e->getMessage() : 'Error.', 500);
        }
    }
}
