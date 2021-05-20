<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
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
            'image_file1'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file2'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file3'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file4'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file5'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file6'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'comment'        => 'nullable|string',
            'public_consent' => 'nullable|boolean',
        ];
    }
}
