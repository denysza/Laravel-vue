<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
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
            'quality'        => 'nullable|numeric|max:5',
            'price'          => 'nullable|numeric|max:5',
            'speed'          => 'nullable|numeric|max:5',
            'correspondence' => 'nullable|numeric|max:5',
            'evaluation'     => 'nullable|string',
        ];
    }
}
