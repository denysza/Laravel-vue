<?php

namespace App\Http\Middleware;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class ImageFilter implements FilterInterface {

    private $is_jpeg = true;

    public function __construct($mime) {
        $this->is_jpeg = preg_match('/image\/jpeg/i', $mime);
    }

    public function applyFilter(Image $img) {
        $max_w = config('const.image.max_width');
        $max_h = config('const.image.max_height');
        $w = $img->width();
        $h = $img->height();

        if ($w > $max_w || $h > $max_h) {
            if ($w / $h > $max_w / $max_h) {
                $img->resize($max_w, null, function($constraint) {
                    $constraint->aspectRatio();
                });
            } else {
                $img->resize(null, $max_h, function($constraint) {
                    $constraint->aspectRatio();
                });
            }
        }

        if ($this->is_jpeg) {
            return $img;
        } else {
            return $img->encode('jpg', 100);
        }

    }

}