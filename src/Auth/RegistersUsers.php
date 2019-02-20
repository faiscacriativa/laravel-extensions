<?php

/**
 * PHP Version 7.2
 *
 * @category Auth
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Auth/RegistersUsers.php
 *
 * TODO: Add tests for RegistersUsers trait
 */

namespace FaiscaCriativa\LaravelExtensions\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers as ParentRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * "Extends" the original RegistersUsers traits to be more API-wise.
 *
 * @category Auth
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Auth/RegistersUsers.php
 */
trait RegistersUsers
{
    use ParentRegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request The incoming request.
     *
     * @return Response The response to be sent to the client.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        if (!$request->wantsJson()) {
            $this->guard()->login($user);
        }

        return $this->registered($request, $user)?: redirect($this->redirectPath());
    }

    /**
     * The user has been registered.
     *
     * @param Request $request The incoming request.
     * @param mixed   $user    The registered user.
     *
     * @return Response|void
     */
    protected function registered(Request $request, $user)
    {
        if ($request->wantsJson()) {
            return response(
                [
                    'error' => false,
                    'data' => ['created' => true]
                ],
                Response::HTTP_CREATED
            );
        }
    }
}
