<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'budget',
        'property_id',
        'category',
        'priority',
        'period',
        'memo',
        'image_file',
    ];

    /**
     * ユーザ
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
