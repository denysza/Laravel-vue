<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserEntryRequest;
use App\Http\Requests\UserEditRequest;
use App\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user')->except(['index', 'create', 'store', 'login', 'detail']);
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
        return User::withTrashed()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.user.entry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\UserEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserEntryRequest $request)
    {
        $user = new User();

        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->name1 = $request->name1;
        $user->name2 = $request->name2;
        $user->message_key = md5(uniqid(rand(), true));  // チャットメッセージの発信元を特定するキー

        $user->save();

        return redirect()->route('user.complete');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = User::find($this->guard()->id());
        $properties = $user->properties;

        return view(config('const.template.user.mypage'), compact('user', 'properties'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view(config('const.template.user.edit'), ['user' => User::find($this->guard()->id())]);
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

        // プロフィール画像保存場所
        $dir = config('const.directory.profile_u');

        // ディレクトリがない場合は作成する
        $dname = storage_path('app/public/' . $dir);

        if (!file_exists($dname)) {
            mkdir($dname, 0777, true);
        }

        $filename = '';
        $del_flg = false;

        // 画像ファイル保存処理
        if ($request->hasFile('image_file')) {
            // リサイズ画像を保存
            $filename = md5(uniqid(rand(), true)) . '.jpg';
            $file = $request->image_file;

            Image::make($file)->filter(new ImageFilter($file->getClientMimeType()))->save($dname . $filename);

            // 新しい画像がアップロードされた場合、登録済みの画像ファイルがあれば削除する
            $del_flg = filled($user->image_file);

            $filename = $dir . $filename;
        } else if (filled($user->image_file)) {
            // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
            $del_flg = $request->boolean('del_flg');
        }

        if ($del_flg) {
            // 古い画像ファイルを削除
            unlink(storage_path('app/public/' . $user->image_file));
        } else {
            // ファイル名変更無
            if (filled($user->image_file)) {
                $filename = $user->image_file;
            }
        }

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
        $user->image_file = $filename;

        $user->save();

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
        //
    }

    /**
     * ログインページ
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            // バリデーション
            $request->validate([
                'email' => 'required|email|max:256',
                'password' => 'required|string|min:8|max:256|alpha_dash',
            ]);

            // 認証処理
            $credentials = $request->only('email', 'password');

            if ($this->guard()->attempt($credentials)) {
                return redirect()->intended(route('user.top'));
            } else {
                return back()->with('status', '認証に失敗しました。');
            }
        }

        return view(config('const.template.user.login'));
    }

    /**
     * トップページ
     *
     * @return \Illuminate\Http\Response
     */
    public function top()
    {
        return view(config('const.template.user.top'), ['user' => User::find($this->guard()->id())]);
    }

    /**
     * ログアウトページ
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard()->logout();

        return redirect()->route('top');
    }

    /**
     * 退会ページ
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request)
    {
        if ($request->isMethod('post')) {
            // バリデーション
            $request->validate([
                'password' => 'password:user',
            ]);

            User::destroy($this->guard()->id());

            $this->guard()->logout();

            // return view(config('const.template.user.withdrawed'));
            return redirect()->route('top');
        }

        return view(config('const.template.user.withdraw'));
    }

    /**
     * 業者向け個人会員詳細ページ
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        return view(config('const.template.user.detail'), ['user' => User::find($id)]);
    }

    /**
     * チャット画面
     *
     * @param  int  $painter_id
     * @param  int  $request_id
     * @return \Illuminate\Http\Response
     */
    public function chat($painter_id, $request_id)
    {
        $proposal = \App\Proposal::where([
            ['painter_id', $painter_id],
            ['request_id', $request_id],
        ])->first();

        $user_id = $this->guard()->id();
        $status = config('const.select.status')[$proposal->status];
        $user = User::find($user_id);
        $message_key = $user->message_key;
        $url = '/user/mypage';

        return view(config('const.template.chat'), compact('user_id', 'painter_id', 'request_id', 'status', 'message_key', 'url'));
    }

    /**
     * 業者検索
     *
     * @return \Illuminate\Http\Response
     */
    public function search_painter()
    {
        return view(config('const.template.user.search_painter'));
    }

    /**
     * 施工事例一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function construction_case_list()
    {
        return view(config('const.template.user.construction_case_list'));
    }

}
