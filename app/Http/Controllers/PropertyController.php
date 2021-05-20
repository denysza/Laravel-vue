<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Http\Requests\PropertyRequest;
use App\Property;

class PropertyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user')->except(['show']);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.property.create'));
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
        $property->area_b = $request->area_b;
        $property->floors = $request->floors;
        $property->age = $request->age;
        $property->type = $request->type;
        $property->type_wall = $request->type_wall;
        $property->type_roof = $request->type_roof;
        $property->repainting_wall = $request->repainting_wall;
        $property->repainting_roof = $request->repainting_roof;
        $property->budget = $request->budget;

        // 物件添付画像保存場所
        $dir = config('const.directory.property');

        // ディレクトリがない場合は作成する
        $dname = storage_path('app/public/' . $dir);

        if (!file_exists($dname)) {
            mkdir($dname, 0777, true);
        }

        for ($i = 1; $i <= 6; $i++) {
            $filename = '';
            $field = 'image_file' . $i;

            // 画像ファイル保存処理
            if ($request->hasFile($field)) {
                // リサイズ画像を保存
                $filename = md5(uniqid(rand(), true)) . '.jpg';
                $file = $request->file($field);

                Image::make($file)->filter(new ImageFilter($file->getClientMimeType()))->save($dname . $filename);

                $filename = $dir . $filename;
            }

            $property->fill([$field => $filename]);
        }

        $property->save();

        // return Property::where('user_id', $this->guard()->id())->get();
        return redirect()->route('user.mypage');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Property::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(config('const.template.property.edit'), ['property' => Property::find($id)]);
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
        $property = Property::find($id);

        $property->name = $request->name;
        $property->address = $request->address;
        $property->area = $request->area;
        $property->area_b = $request->area_b;
        $property->floors = $request->floors;
        $property->age = $request->age;
        $property->type = $request->type;
        $property->type_wall = $request->type_wall;
        $property->type_roof = $request->type_roof;
        $property->repainting_wall = $request->repainting_wall;
        $property->repainting_roof = $request->repainting_roof;
        $property->budget = $request->budget;

        // 物件添付画像保存場所
        $dir = config('const.directory.property');

        // ディレクトリがない場合は作成する
        $dname = storage_path('app/public/' . $dir);

        if (!file_exists($dname)) {
            mkdir($dname, 0777, true);
        }

        for ($i = 1; $i <= 6; $i++) {
            $filename = '';
            $del_flg = false;
            $field = 'image_file' . $i;

            // 画像ファイル保存処理
            if ($request->hasFile($field)) {
                // リサイズ画像を保存
                $filename = md5(uniqid(rand(), true)) . '.jpg';
                $file = $request->file($field);

                Image::make($file)->filter(new ImageFilter($file->getClientMimeType()))->save($dname . $filename);

                // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
                $del_flg = filled($property->value($field));

                $filename = $dir . $filename;
            } else if (filled($property->value($field))) {
                // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
                $del_flg = $request->boolean('del_flg' . $i);
            }

            if ($del_flg) {
                // 古い画像ファイルを削除
                unlink(storage_path('app/public/' . $property->value($field)));
            } else {
                // ファイル名変更無
                if (filled($property->value($field))) {
                    $filename = $property->value($field);
                }
            }

            $property->fill([$field => $filename]);
        }

        $property->save();

        // return Property::where('user_id', $this->guard()->id())->get();
        return redirect()->route('user.mypage');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Property::destroy($id);

        // return Property::where('user_id', $this->guard()->id())->get();
        return redirect()->route('user.mypage');
    }
}
