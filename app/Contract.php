<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'request_id',
        'painter_id',
        'category',
        'document',
        'contract_amount',
        'contract_date',
        'contract_details',
        'charge_name',
        'plan',
        'period',
        'paint',
        'warranty_title',
        'warranty',
        'memo',
        'complete_date',
        'amount',
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
