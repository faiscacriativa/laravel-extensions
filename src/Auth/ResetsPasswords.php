<?php

namespace FaiscaCriativa\LaravelExtensions\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords as ParentResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

trait ResetsPasswords
{
    use ParentResetsPasswords;

    /**
     * Get the guard to be used during password reset.
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
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        if (!request()->wantsJson()) {
            $user->setRememberToken(Str::random(60));
        }

        $user->save();

        event(new PasswordReset($user));

        if (!request()->wantsJson()) {
            $this->guard()->login($user);
        }
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return response()->json(
                [
                    'error' => false,
                    'message' => trans($response)
                ],
                Response::HTTP_OK
            );
        }

        return redirect($this->redirectPath())
            ->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            $data = [
                'error'   => true,
                'message' => trans($response)
            ];

            switch ($response) {
            case Password::INVALID_TOKEN:
                $data['data'] = 'invalid_token';
                break;

            case Password::INVALID_USER:
                $data['data'] = [
                    [
                        'field' => 'email',
                        'message' => $data['message']
                    ]
                ];
            }

            return response()->json(
                $data,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
