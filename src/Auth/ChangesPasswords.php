<?php

namespace FaiscaCriativa\LaravelExtensions\Auth;

use FaiscaCriativa\LaravelExtensions\Http\Requests\ChangePassword;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

trait ChangesPassword
{
    /**
     * Change user password.
     *
     * @param \FaiscaCriativa\LaravelExtensions\Http\Requests\ChangePassword $request A requisição recebida.
     *
     * @return void
     */
    public function change(ChangePassword $request)
    {
        $user = Auth::user();
        $data = $request->only(['current_password', 'password']);

        if (!Hash::check($data['current_password'], $user->password)) {
            $errors = [
                [
                    'field' => 'current_password',
                    'message' => Lang::getfromJson('The current password is wrong.')
                ]
            ];

            return response()->json(
                [
                    'error'   => true,
                    'message' => Lang::getFromJson('Please, check the input data.'),
                    'data'    => $errors
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(
            [
                'error'   => false,
                'message' => Lang::getfromJson('Password changed sucessfully.')
            ],
            Response::HTTP_OK
        );
    }
}
