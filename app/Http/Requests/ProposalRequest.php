<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
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
            'visit_schedule' => 'nullable|date|after:today',
            'visit_record'   => 'nullable|date',
            'bulk_flg'       => 'nullable|numeric',
            'quotation1'     => "nullable|file|max:{$max_size}|mimes:pdf",
            'quotation2'     => "nullable|file|max:{$max_size}|mimes:pdf",
            'quotation3'     => "nullable|file|max:{$max_size}|mimes:pdf",
            'quotation4'     => "nullable|file|max:{$max_size}|mimes:pdf",
            'quotation5'     => "nullable|file|max:{$max_size}|mimes:pdf",
            'document1'      => "nullable|file|max:{$max_size}|mimes:pdf",
            'document2'      => "nullable|file|max:{$max_size}|mimes:pdf",
            'document3'      => "nullable|file|max:{$max_size}|mimes:pdf",
            'document4'      => "nullable|file|max:{$max_size}|mimes:pdf",
            'document5'      => "nullable|file|max:{$max_size}|mimes:pdf",
            'user_memo'      => 'nullable|string',
            'painter_memo'   => 'nullable|string',
            'visit_memo'     => 'nullable|string',
            'quotation_memo' => 'nullable|string',
        ];
    }
}
