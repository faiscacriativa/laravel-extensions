<?php

namespace FaiscaCriativa\LaravelExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePassword extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => 'required|string|min:6|max:255',
            'password' => 'required|string|confirmed|min:6|max:255',
            'password_confirmation' => 'required|string|min:6|max:255'
        ];
    }
}
