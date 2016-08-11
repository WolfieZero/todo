<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SignupRequest extends Request
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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     => 'A name is required.',
            'email.required'    => 'An email address is required.',
            'email.email'       => 'The email address does not appear valid.',
            'email.unique'      => 'That email address is already registered.',
            'password.required' => 'A password is required.',
            'password.min'      => 'Password should have at least 6 characters (anything will do).'
        ];
    }
}
