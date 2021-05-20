<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'painter_id',
        'user_id',
        'property_id',
        'example_id',
        'column_id',
        'image_file',
        'flg',
    ];

    /**
     * デフォルト値
     *
     * @var array
     */
    protected $attributes = [
        'painter_id' => 0,
        'user_id' => 0,
        'property_id' => 0,
        'example_id' => 0,
        'column_id' => 0,
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
