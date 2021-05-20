<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
            'name'            => 'nullable|string|max:48',
            'address'         => 'nullable|string|max:256',
            'area'            => 'nullable|numeric|max:9999',
            'area_b'          => 'nullable|numeric|max:9999',
            'floors'          => 'nullable|numeric|max:99',
            'age'             => 'nullable|numeric|max:99',
            'type'            => 'nullable|numeric',
            'type_wall'       => 'nullable|numeric',
            'type_roof'       => 'nullable|numeric',
            'repainting_wall' => 'nullable|numeric|max:99',
            'repainting_roof' => 'nullable|numeric|max:99',
            'budget'          => 'nullable|numeric|max:9999', // 万円単位
            'image_file1'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file2'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file3'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file4'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file5'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file6'     => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
        ];
    }
}
