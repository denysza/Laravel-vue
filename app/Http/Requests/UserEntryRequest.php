<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEntryRequest extends FormRequest
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
            'email'    => 'required|email|max:256|unique:users',
            'password' => 'required|string|min:8|max:256|alpha_dash|confirmed',
            'name1'    => 'required|string|max:48',
            'name2'    => 'required|string|max:48',
        ];
    }
}
