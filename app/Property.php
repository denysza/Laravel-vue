<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Property extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'name',
        'address',
        'area',
        'area_b',
        'floors',
        'age',
        'type',
        'num',
        'type_wall',
        'type_roof',
        'repainting_wall',
        'repainting_roof',
        'budget',
        'image_file1',
        'image_file2',
        'image_file3',
        'image_file4',
        'image_file5',
        'image_file6',
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
     * 画像1URL
     *
     * @return string
     */
    public function getImage1Attribute()
    {
        $value = $this->image_file1;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像1があるか
     *
     * @return boolean
     */
    public function getImage1ExistsAttribute()
    {
        $value = $this->image_file1;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }

    /**
     * 画像2URL
     *
     * @return string
     */
    public function getImage2Attribute()
    {
        $value = $this->image_file2;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像2があるか
     *
     * @return boolean
     */
    public function getImage2ExistsAttribute()
    {
        $value = $this->image_file2;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }

    /**
     * 画像3URL
     *
     * @return string
     */
    public function getImage3Attribute()
    {
        $value = $this->image_file3;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像3があるか
     *
     * @return boolean
     */
    public function getImage3ExistsAttribute()
    {
        $value = $this->image_file3;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }

    /**
     * 画像4URL
     *
     * @return string
     */
    public function getImage4Attribute()
    {
        $value = $this->image_file4;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像4があるか
     *
     * @return boolean
     */
    public function getImage4ExistsAttribute()
    {
        $value = $this->image_file4;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }

    /**
     * 画像5URL
     *
     * @return string
     */
    public function getImage5Attribute()
    {
        $value = $this->image_file5;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像5があるか
     *
     * @return boolean
     */
    public function getImage5ExistsAttribute()
    {
        $value = $this->image_file5;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }

    /**
     * 画像6URL
     *
     * @return string
     */
    public function getImage6Attribute()
    {
        $value = $this->image_file6;
        $storage = Storage::disk('property');

        if ($storage->exists($value)) {
            return $storage->url($value);
        }

        return config('const.no_image');
    }

    /**
     * 画像6があるか
     *
     * @return boolean
     */
    public function getImage6ExistsAttribute()
    {
        $value = $this->image_file6;
        $storage = Storage::disk('property');

        return $storage->exists($value);
    }
}
