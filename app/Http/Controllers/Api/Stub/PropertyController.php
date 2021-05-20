<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PropertyRequest;
use App\Property;
use Image;
use App\Http\Middleware\ImageFilter;
use Storage;

class PropertyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    /**
     * Get the guard.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\PropertyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {
        $property = new Property();

        $property->user_id = $this->guard()->id();
        $property->name = $request->name;
        $property->address = $request->address;
        $property->area = $request->area;
        $property->floors = $request->floors;
        $property->age = $request->age;
        $property->type = $request->type;
        $property->type_wall = $request->type_wall;
        $property->type_roof = $request->type_roof;
        $property->repainting_wall = $request->repainting_wall;
        $property->repainting_roof = $request->repainting_roof;
        $property->budget = $request->budget;

        $storage = Storage::disk('property');

        for ($i = 1; $i <= 6; $i++) {
            $field = 'image_file' . $i;
            $prop = 'image' . $i;

            // 画像ファイル保存処理
            if ($request->hasFile($field)) {
                $filename = md5(uniqid(rand(), true)) . '.jpg';
                $file = $request->file($field);
                $image = Image::make($file)->filter(new ImageFilter($file->getClientMimeType()));

                if ($storage->put($filename, $image)) {
                    // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
                    if ($property->{$prop . 'Exists'}) {
                        $storage->delete($property->{$field});
                    }
        
                    $property->{$field} = $filename;
                }
            }
        }

        $property->save();
        return response()->json();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\PropertyRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyRequest $request, $id)
    {
        $user = $this->guard()->user();
        $property = $user->properties()->where('id', $id)->first();

        if (!$property) {
            return response()->json([], 403);
        }

        $property->name = $request->name;
        $property->address = $request->address;
        $property->area = $request->area;
        $property->floors = $request->floors;
        $property->age = $request->age;
        $property->type = $request->type;
        $property->type_wall = $request->type_wall;
        $property->type_roof = $request->type_roof;
        $property->repainting_wall = $request->repainting_wall;
        $property->repainting_roof = $request->repainting_roof;
        $property->budget = $request->budget;

        $storage = Storage::disk('property');

        for ($i = 1; $i <= 6; $i++) {
            $field = 'image_file' . $i;
            $prop = 'image' . $i;

            // 画像ファイル保存処理
            if ($request->hasFile($field)) {
                $filename = md5(uniqid(rand(), true)) . '.jpg';
                $file = $request->file($field);
                $image = Image::make($file)->filter(new ImageFilter($file->getClientMimeType()));

                if ($storage->put($filename, $image)) {
                    // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
                    if ($property->{$prop . 'Exists'}) {
                        $storage->delete($property->{$field});
                    }
        
                    $property->{$field} = $filename;
                }
            } elseif ($request->boolean('del_flg' . $i) && $property->{$prop . 'Exists'}) {
                // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
                $storage->delete($property->{$field});
                $property->{$field} = null;
            }
        }

        $property->save();
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->guard()->user();
        $property = $user->properties()->where('id', $id)->first();

        if (!$property) {
            return response()->json([], 403);
        }

        $storage = Storage::disk('property');

        for ($i = 1; $i <= 6; $i++) {
            $field = 'image_file' . $i;
            $prop = 'image' . $i;

            if ($property->{$prop . 'Exists'}) {
                // 登録済みの画像ファイル削除
                $storage->delete($property->{$field});
            }
        }

        $property->delete();
        return response()->json();
    }
}
