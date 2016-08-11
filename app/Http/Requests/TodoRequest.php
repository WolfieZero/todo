<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TodoRequest extends Request
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
            'task' => 'required',
            'user_id' => 'required|integer'
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
            'name.required'    => '`task` is required.',
            'user_id.required' => '`user_id` is required.',
            'user_id.integer'  => '`user_id` needs to be a interger.',
        ];
    }
}
