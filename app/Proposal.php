<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'painter_id', 
        'request_id', 
        'visit_schedule',
        'visit_record',
        'bulk_flg',
        'quotation1',
        'quotation2',
        'quotation3',
        'quotation4',
        'quotation5',
        'document1',
        'document2',
        'document3',
        'document4',
        'document5',
        'user_memo',
        'painter_memo',
        'visit_memo',
        'quotation_memo',
        'status',
    ];

    /**
     * ステータスのデフォルト値：0（新規）
     *
     * @var array
     */
    protected $attributes = [
        'status' => 0,
    ];

    /**
     * 業者
     *
     * @return Painter
     */
    public function painter()
    {
        return $this->belongsTo('App\Painter');
    }
}
