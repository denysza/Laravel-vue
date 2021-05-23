<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTwitterAccount extends Model
{
    //
    protected $fillable = ['user_id', 'provider_user_id', 'provider'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
