<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\PainterEntryRequest;
use App\Http\Requests\PainterEditRequest;
use App\Painter;
use Image;
use App\Http\Middleware\ImageFilter;
use Storage;
use App\Image as ImageTable;
use App\Http\Requests\PropertyRequest;
use App\Example;
use App\Property;
use App\Contract;

class PainterController extends Controller
{
    /**
     * Get the guard.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('painter');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\PainterEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function entry(PainterEntryRequest $request)
    {
        $painter = new Painter();

        $painter->email = $request->email;
        $painter->password = bcrypt($request->password);
        $painter->name = $request->name;
        $painter->message_key = md5(uniqid(rand(), true));  // チャットメッセージの発信元を特定するキー

        $painter->save();

        return response()->json([
            'next' => route('painter.complete')
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // バリデーション
        $request->validate([
            'email' => 'required|email|max:256',
            'password' => 'required|string|min:8|max:256|alpha_dash',
        ]);

        // 認証処理
        $credentials = $request->only('email', 'password');

        if (Auth::guard('painter')->attempt($credentials)) {
            return response()->json([
                'next' => route('painter.top')
            ]);
        }

        return response()->json([
            'errors' => [
                'email' => [__('auth.failed')]
            ]
        ], 401);
    }
    
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $painter = Painter::find($this->guard()->id());

        return response()->json($painter->setAppends(['profile_image'])->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\PainterEditRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(PainterEditRequest $request)
    {
        $painter = Painter::find($this->guard()->id());

        $painter->name = $request->name;
        $painter->kana = $request->kana;
        $painter->ceo_name = $request->ceo_name;
        $painter->type = $request->type;
        $painter->prefectures = $request->prefectures;
        $painter->city = $request->city;
        $painter->address1 = $request->address1;
        $painter->address2 = $request->address2;
        $painter->tel = $request->tel;
        $painter->fax = $request->fax;
        $painter->charge_name1 = $request->charge_name1;
        $painter->charge_name2 = $request->charge_name2;
        $painter->charge_kana1 = $request->charge_kana1;
        $painter->charge_kana2 = $request->charge_kana2;
        $painter->charge_tel = $request->charge_tel;
        $painter->charge_email = $request->charge_email;
        $painter->url = $request->url;
        $painter->established = $request->established;
        $painter->capital = $request->capital;
        $painter->permission = $request->permission;
        $painter->organization = $request->organization;
        $painter->sales = $request->sales;
        $painter->employees = $request->employees;
        $painter->social_insurance = $request->social_insurance;
        $painter->accident_insurance = $request->accident_insurance;
        $painter->other_insurance = $request->other_insurance;
        $painter->category = $request->category;
        $painter->catch_copy = $request->catch_copy;
        $painter->pr_copy = $request->pr_copy;

        $painter->save();

        return response()->json();
    }

    /**
     * プロフィール画像アップロード
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request)
    {
        $max_size = config('const.image.max_size');

        // バリデーション
        $request->validate([
            'image_file' => "required|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
        ]);

        $painter = Painter::find($this->guard()->id());
        $storage = Storage::disk('profile_p');

        // リサイズ画像を保存
        $filename = md5(uniqid(rand(), true)) . '.jpg';
        $file = $request->image_file;
        $image = Image::make($file)->filter(new ImageFilter($file->getClientMimeType()));

        if ($storage->put($filename, $image)) {
            // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
            if ($storage->exists($painter->image_file)) {
                $storage->delete($painter->image_file);
            }

            $painter->image_file = $filename;
            $painter->save();
        }

        return response()->json([
            'profile_image' => $painter->profile_image,
        ]);
    }

    /**
     * プロフィール画像削除
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteImage()
    {
        $painter = Painter::find($this->guard()->id());

        $storage = Storage::disk('profile_p');
        if ($storage->exists($painter->image_file)) {
            $storage->delete($painter->image_file);
            $painter->image_file = null;
            $painter->save();
        }

        return response()->json([
            'profile_image' => config('const.no_image'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\PropertyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {

        $pid = $this->guard()->id();
        $painter = Painter::find($pid);

        // 現在の登録画像を取得
        $imageDB = ImageTable::where('painter_id', '=', $pid);

        // ID順
        $imageDB = $imageDB->orderby('id', 'asc');

        $array = $imageDB->select('id')->get();
        $array_length = count($array);
        
        $storage = Storage::disk('profile_p');

        for ($i = 1; $i <= 6; $i++) {
        
            $imageStore = new ImageTable();

            if ($array_length < $i ){
                //新規登録
                $imageStore->painter_id = $pid;
            } else {
                //上書き登録
                $imageStore = ImageTable::find($array[$i - 1]);
            }

            $field = 'image_file' . $i;
            $prop = 'image' . $i;


            // 画像ファイル保存処理
            if ($request->hasFile($field)) {

                $filename = md5(uniqid(rand(), true)) . '.jpg';
                $file = $request->file($field);
                $image =Image::make($file)->filter(new ImageFilter($file->getClientMimeType()));

                if ($storage->put($filename, $image)) {
                    // 登録済みの画像ファイルがあれば削除する
                    if ($storage->exists($imageStore->image_file)) {
                        $storage->delete($imageStore->image_file);
                    }

                    //プロフィール画像指定
                    //todo 現状はとりあえず１番目を指定
                    if ($i == 1) {
                        $imageStore->flg = 1;

                        $painter->image_file = $filename;
                        $painter->save();

                    } else {
                        $imageStore->flg = 0;
                    }
                    
                    $imageStore->image_file = $filename;
                    $imageStore->save();
                }
            }
        }

        return response()->json();
    }

    /**
     * 画像取得
     *
     * @return \Illuminate\Http\Response
     */
    public function images()
    {
        $pid = $this->guard()->id();

        // 現在の登録画像を取得
        $imageDB = ImageTable::where('painter_id', '=', $pid);

        // ID順
        $imageDB = $imageDB->orderby('id', 'asc');

        return $imageDB->select('image_file')->get();
    }

    /**
     * サイト外施工事例新規作成処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exampleentry(Request $request)
    {
        // 物件データを追加
        $property_id = Property::insertGetId([
            'user_id' => 0, 
            'name' => $request->name, 
            'address' => $request->address,
            'area' => $request->area,
            'area_b' => $request->area_b,
            'floors' => $request->floors,
            'age' => $request->age,
            'type' => $request->type,
            'type_wall' => $request->type_wall,
            'type_roof' => $request->type_roof
        ]);

        // 契約データを追加
        $contract_id = Contract::insertGetId([
            'user_id' => 0, 
            'property_id' => $property_id, 
            'request_id' => 0, 
            'painter_id' => $this->guard()->id(), 
            'category' => $request->category, 
            'plan' => $request->plan, 
            'period' => $request->period, 
            'paint' => $request->paint, 
            'memo' => $request->memo, 
            'complete_date' => $request->complete_date, 
            'amount' => $request->amount,
            'warranty_title' => $request->warranty_title,
            'warranty' => $request->warranty
        ]);
        // 施工事例データを追加
        $example = new Example();

        $example->painter_id = $this->guard()->id();
        $example->contract_id = $contract_id;

        // 施工事例画像保存場所
        $dir = config('const.directory.example');

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

            $example->fill([$field => $filename]);
        }

        $example->comment = $request->comment;

        // とりあえず強制公開
//        $example->public_consent = $request->public_consent;
        $example->public_consent = 1;

        $example->save();

        // return Example::where('painter_id', $this->guard()->id())->get();
        return response()->json([
            'next' => route('painter.mypage')
        ]);
    }
}
