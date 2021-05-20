<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestRequest extends FormRequest
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
            'title'      => 'nullable|string|max:256',
            'budget'     => 'nullable|numeric|max:9999', // 万円単位
            'category'   => 'nullable|numeric',
            'image_file' => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
        ];
    }
}
