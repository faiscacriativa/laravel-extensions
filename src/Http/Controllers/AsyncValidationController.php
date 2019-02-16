<?php

/**
 * PHP Version 7.2
 *
 * @category Controllers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Controllers/AsyncValidationController.php
 */
namespace FaiscaCriativa\LaravelExtensions\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Asyncronous validations controller.
 *
 * @category Controllers
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/Http/Controllers/AsyncValidationController.php
 */
class AsyncValidationController extends Controller
{
    /**
     * The name of the field used to store email addresses.
     *
     * @var string
     */
    protected $emailField = 'email';

    /**
     * The name of the database table that users are stored.
     *
     * @var string
     */
    protected $userTable = 'users';

    /**
     * Check the provided e-mail availability in the users table.
     *
     * @param Request $request The incoming request.
     *
     * @return Response
     */
    public function checkEmailAvailability(Request $request)
    {
        $validator = Validator::make(
            [$this->emailField => $request->input($this->emailField)],
            [$this->emailField => 'required|email|unique:' . $this->userTable]
        );

        if ($validator->fails()) {
            $failed = $validator->failed();
            $errors = $validator->errors();

            $statusCode   = Response::HTTP_INTERNAL_SERVER_ERROR;
            $responseBody = [];

            if (array_has($failed, 'email.Required')
                || array_has($failed, 'email.Email')
            ) {
                $statusCode   = Response::HTTP_UNPROCESSABLE_ENTITY;
                $responseBody = [
                    'error'   => true,
                    'message' => $errors->get($this->emailField)[0]
                ];
            }

            if (array_has($failed, 'email.Unique')) {
                $statusCode   = Response::HTTP_OK;
                $responseBody = [
                    'error'   => false,
                    'data'    => ['available' => false]
                ];
            }

            return response()->json($responseBody, $statusCode);
        }

        return response()
            ->json(
                [
                    'error'   => false,
                    'data'    => ['available' => true]
                ],
                Response::HTTP_OK
            );
    }
}
