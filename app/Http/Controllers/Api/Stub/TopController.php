<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;

class TopController extends Controller
{
    public function painters()
    {
        return response()->json([
            [
                'title' => '塗装業者1',
                'description' => '塗装します'
            ],
            [
                'title' => '塗装業者2',
                'description' => '塗装しますか'
            ],
            [
                'title' => '塗装業者3',
                'description' => '塗装しますよ'
            ],
            [
                'title' => '塗装業者4',
                'description' => '塗装しますけど'
            ],
            [
                'title' => '塗装業者5',
                'description' => '塗装しますね'
            ],
            [
                'title' => '塗装業者6',
                'description' => '塗装しました'
            ],
        ]);
    }

    public function exsamples()
    {
        return response()->json([
            [
                'title' => '事例1',
                'description' => '○○アパート'
            ],
            [
                'title' => '事例2',
                'description' => '××マンション'
            ],
            [
                'title' => '事例3',
                'description' => '○○工場'
            ],
            [
                'title' => '事例4',
                'description' => '××ビル'
            ],
            [
                'title' => '事例5',
                'description' => '○○店'
            ],
            [
                'title' => '事例6',
                'description' => '××家'
            ],
        ]);
    }

    public function columns()
    {
        return response()->json([
            [
                'title' => 'コラム1',
                'description' => 'すごい'
            ],
            [
                'title' => 'コラム2',
                'description' => '早い'
            ],
            [
                'title' => 'コラム3',
                'description' => '丁寧'
            ],
            [
                'title' => 'コラム4',
                'description' => '施工'
            ],
            [
                'title' => 'コラム5',
                'description' => '塗装'
            ],
            [
                'title' => 'コラム6',
                'description' => '道場'
            ],
        ]);
    }
}