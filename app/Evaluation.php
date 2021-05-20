<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'painter_id',
        'contract_id',
        'quality',
        'price',
        'speed',
        'correspondence',
        'evaluation',
        'flg',
    ];

    /**
     * 評価済フラグのデフォルト値：0（未評価）
     *
     * @var array
     */
    protected $attributes = [
        'flg' => 0,
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
