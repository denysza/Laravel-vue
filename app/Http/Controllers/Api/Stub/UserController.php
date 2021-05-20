<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserEntryRequest;
use App\Http\Requests\UserEditRequest;
use App\User;
use Image;
use App\Http\Middleware\ImageFilter;
use Storage;
use App\Painter;
use App\Example;
use App\Contract;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user')->except(['entry', 'login']);
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
     * @param  \App\Http\Request\UserEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function entry(UserEntryRequest $request)
    {
        $user = new User();

        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->name1 = $request->name1;
        $user->name2 = $request->name2;
        $user->message_key = md5(uniqid(rand(), true));  // チャットメッセージの発信元を特定するキー

        $user->save();

        return response()->json([
            'next' => route('user.complete')
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

        if ($this->guard()->attempt($credentials)) {
            return response()->json([
                'next' => route('user.top')
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
        $user = User::find($this->guard()->id());

        return response()->json($user->setAppends(['profile_image'])->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\UserEditRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserEditRequest $request)
    {
        $user = User::find($this->guard()->id());

        $user->name1 = $request->name1;
        $user->name2 = $request->name2;
        $user->kana1 = $request->kana1;
        $user->kana2 = $request->kana2;
        $user->nickname = $request->nickname;
        $user->postal = $request->postal;
        $user->prefectures = $request->prefectures;
        $user->city = $request->city;
        $user->address1 = $request->address1;
        $user->address2 = $request->address2;
        $user->tel = $request->tel;
        $user->mobile = $request->mobile;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;

        $user->save();

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

        $user = User::find($this->guard()->id());
        $storage = Storage::disk('profile_u');

        // リサイズ画像を保存
        $filename = md5(uniqid(rand(), true)) . '.jpg';
        $file = $request->image_file;
        $image = Image::make($file)->filter(new ImageFilter($file->getClientMimeType()));

        if ($storage->put($filename, $image)) {
            // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
            if ($storage->exists($user->image_file)) {
                $storage->delete($user->image_file);
            }

            $user->image_file = $filename;
            $user->save();
        }

        return response()->json([
            'profile_image' => $user->profile_image,
        ]);
    }

    /**
     * プロフィール画像削除
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteImage()
    {
        $user = User::find($this->guard()->id());

        $storage = Storage::disk('profile_u');
        if ($storage->exists($user->image_file)) {
            $storage->delete($user->image_file);
            $user->image_file = null;
            $user->save();
        }

        return response()->json([
            'profile_image' => config('const.no_image'),
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function properties()
    {
        $user = User::find($this->guard()->id());

        $properties = $user->properties()->get()->map(function($property) {
            return $property->setAppends(['image1', 'image2', 'image3', 'image4', 'image5', 'image6']);
        });

        return response()->json($properties->toArray());
    }

    /**
     * 業者検索
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        // ランクが1以上（管理者により認証済み）の業者会員を対象とする
        // $painter = Painter::where('rank', '>=', 1);
        $painter = Painter::where('rank', '>=', 0);

        if ($request->filled('prefectures')) {
            // 都道府県で検索
            $painter = $painter->where('prefectures', $request->prefectures);
        }

        if ($request->filled('city')) {
            // 市町村で検索
            $keyword1 = '%' . mb_convert_kana($request->city, 'aCKV') . '%';
            $keyword2 = '%' . $request->city . '%';

            $painter = $painter->where(function($q) use ($keyword1, $keyword2) {
                $q->where('city', 'like', $keyword1)
                  ->orWhere('city', 'like', $keyword2);
            });
        }

        if ($request->filled('address')) {
            // 住所で検索
            $keyword1 = '%' . mb_convert_kana($request->address, 'aCKV') . '%';
            $keyword2 = '%' . $request->address . '%';

            $painter = $painter->where(function($q) use ($keyword1, $keyword2) {
                $q->where('address1', 'like', $keyword1)
                  ->orWhere('address1', 'like', $keyword2)
                  ->orWhere('address2', 'like', $keyword1)
                  ->orWhere('address2', 'like', $keyword2);
            });
        }

        if ($request->filled('name')) {
            // 事業所名・フリガナで検索
            $keyword1 = '%' . mb_convert_kana($request->name, 'aCKV') . '%';
            $keyword2 = '%' . $request->name . '%';

            $painter = $painter->where(function($q) use ($keyword1, $keyword2) {
                $q->where('name', 'like', $keyword1)
                  ->orWhere('name', 'like', $keyword2)
                  ->orWhere('kana', 'like', $keyword1)
                  ->orWhere('kana', 'like', $keyword2);
            });
        }

        if ($request->filled('category')) {
            // 業務内容カテゴリーで検索
            $painter = $painter->where('category', $request->category);
        }

        // １回あたりの表示件数
        $limit = $request->input('limit', 20);
        // 取得位置
        $skip = $request->input('skip', 0);

        // ランクの高い順に取得する
        // TODO:将来的には並べ替え対象を引数で受け取る
        $painter = $painter->orderby('rank', 'desc')->orderby('id', 'asc');

        // 表示件数分取得
        $painter = $painter->skip($skip)->limit($limit);

        // return $painter;
        return $painter->get();
    }

    /**
     * 施工事例データ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function examplelist(Request $request)
    {
        // 公開承諾済データのみ取得
        $example = Example::where('public_consent', 1);

        if ($request->has('painter_id')) {
            $painter_id = $request->painter_id;

            // 業者IDが指定されていれば、指定の業者のデータのみ取得
            if (is_array($painter_id)) {
                $example = $example->whereIn('painter_id', $painter_id);
            } else {
                $example = $example->where('painter_id', $painter_id);
            }
        }

        // 契約、業者JOIN
        $example = $example->join('painters', 'examples.painter_id', '=', 'painters.id')
            ->join('contracts', 'examples.contract_id', '=', 'contracts.id')
            ->join('properties', 'contracts.property_id', '=', 'properties.id')
            ->select('examples.id','examples.image_file1','examples.image_file2','examples.image_file3','examples.image_file4','examples.image_file5','examples.image_file6','examples.deleted_at','examples.created_at','examples.updated_at','examples.comment','properties.name','properties.type','properties.address','contracts.category','contracts.period','contracts.warranty');

        // １回あたりの表示件数
        $limit = $request->input('limit', 20);
        // 取得位置
        $skip = $request->input('skip', 0);

        // 最新のデータから順に取得
        $example = $example->orderby('examples.updated_at', 'desc');

        // 表示件数分取得
        $example = $example->skip($skip)->limit($limit);

        // return $example;
        return $example->get();
    }

}
