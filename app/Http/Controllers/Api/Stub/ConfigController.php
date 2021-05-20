<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function select()
    {
        return response()->json(config('const.select'));
    }
}