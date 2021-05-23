<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword;
use Storage;

class User extends Model implements Authenticatable, CanResetPassword
{
    use SoftDeletes, Notifiable;

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        //
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        //
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        //
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token, 'users'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'image_file'
    ];

    /**
     * 配列に含めない属性
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];


    /**
     * 物件
     *
     * @return Property
     */
    public function properties()
    {
        return $this->hasMany('App\Property');
    }


    /**
     * プロフィール画像URL
     *
     * @return string
     */
    public function getProfileImageAttribute()
    {
        $value = $this->image_file;
        $storage = Storage::disk('profile_u');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * プロフィール画像があるか
     *
     * @return boolean
     */
    public function getProfileImageExistsAttribute()
    {
        $value = $this->image_file;
        $storage = Storage::disk('profile_u');

        return $storage->exists($value);
    }
}
