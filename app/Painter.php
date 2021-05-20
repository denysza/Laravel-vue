<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword;
use Storage;

class Painter extends Model implements Authenticatable, CanResetPassword
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
        $this->notify(new ResetPassword($token, 'painters'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'kana',
        'ceo_name',
        'type',
        'prefectures',
        'city',
        'address1',
        'address2',
        'tel',
        'fax',
        'charge_name1',
        'charge_name2',
        'charge_kana1',
        'charge_kana2',
        'charge_tel',
        'charge_email',
        'url',
        'established',
        'capital',
        'permission',
        'organization',
        'sales',
        'employees',
        'social_insurance',
        'accident_insurance',
        'other_insurance',
        'category',
        'image_file',
        'catch_copy',
        'constructions',
        'rank',
        'pr_copy',
        'message_key',
    ];

    /**
     * ランクのデフォルト値：0
     *
     * @var array
     */
    protected $attributes = [
        'rank' => 0,
    ];

    /**
     * 施工事例
     *
     * @return Example
     */
    public function examples()
    {
        return $this->hasMany('App\Example');
    }

    /**
     * コラム
     *
     * @return Column
     */
    public function columns()
    {
        return $this->hasMany('App\Column');
    }

    /**
     * 提案
     *
     * @return Proposal
     */
    public function proposals()
    {
        return $this->hasMany('App\Proposal');
    }

    /**
     * 契約
     *
     * @return Contract
     */
    public function contracts()
    {
        return $this->hasMany('App\Contract');
    }

    /**
     * 評価
     *
     * @return Evaluation
     */
    public function evaluations()
    {
        return $this->hasMany('App\Evaluation');
    }

    /**
     * お気に入り業者
     *
     * @return Favorite
     */
    public function favorites()
    {
        return $this->hasMany('App\Favorite');
    }

    /**
     * 画像ファイル
     *
     * @return Image
     */
    public function images()
    {
        return $this->hasMany('App\Image');
    }

    /**
     * プロフィール画像URL
     *
     * @return string
     */
    public function getProfileImageAttribute()
    {
        $value = $this->image_file;
        $storage = Storage::disk('profile_p');

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
        $storage = Storage::disk('profile_p');

        return $storage->exists($value);
    }
}
