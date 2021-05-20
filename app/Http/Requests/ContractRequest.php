<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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
        // 添付書類のファイルサイズ上限：1024 x 1024 (KB) = 1GB
        $max_size = 1024 * 1024;

        return [
            'category'         => 'nullable|numeric',
            'document'         => "nullable|file|max:{$max_size}|mimes:pdf",
            'contract_amount'  => 'nullable|numeric|max:99999999', // 円単位
            'contract_date'    => 'nullable|date|after:today',
            'contract_details' => 'nullable|string|max:256',
            'charge_name'      => 'nullable|string|max:256',
            'plan'             => 'nullable|string|max:256',
            'period'           => 'nullable|numeric|max:999',
            'paint'            => 'nullable|string|max:256',
            'memo'             => 'nullable|string',
            'complete_date'    => 'nullable|date',
            'amount'           => 'nullable|numeric|max:99999999', // 円単位
        ];
    }
}
