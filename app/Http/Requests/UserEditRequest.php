<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
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
        $max_size = config('const.image.max_size');

        return [
            'name1'       => 'required|string|max:48',
            'name2'       => 'required|string|max:48',
            'kana1'       => 'nullable|string|max:48',
            'kana2'       => 'nullable|string|max:48',
            'nickname'    => 'nullable|string|max:48',
            'postal'      => 'nullable|string|max:8',
            'prefectures' => 'nullable|numeric',
            'city'        => 'nullable|string|max:20',
            'address1'    => 'nullable|string|max:256',
            'address2'    => 'nullable|string|max:256',
            'tel'         => 'nullable|string|max:20',
            'mobile'      => 'nullable|string|max:20',
            'birth_date'  => 'nullable|date',
            'gender'      => 'nullable|numeric',
            'image_file'  => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
        ];
    }
}
