<?php

namespace FaiscaCriativa\LaravelExtensions\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails as ParentSendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait SendsPasswordResetEmails
{
    use ParentSendsPasswordResetEmails;

    /**
     * Get the response for a successful password reset link.
     *
     * @param \Illuminate\Http\Request $request  The incoming request.
     * @param string                   $response The response to be sent to the client.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return response()->json(
                [
                    'error'   => false,
                    'message' => trans($response)
                ],
                Response::HTTP_OK
            );
        }

        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param \Illuminate\Http\Request $request  The incoming request.
     * @param string                   $response The response to be sent to the client.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return response()->json(
                [
                    'error'   => true,
                    'data'    => [
                        ['field' => 'email', 'message' => trans($response)]
                    ],
                    'message' => trans('validation.verify_prompt')
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
