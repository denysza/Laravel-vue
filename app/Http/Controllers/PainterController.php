<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PainterEntryRequest;
use App\Http\Requests\PainterEditRequest;
use App\Painter;

class PainterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter')->except(['index', 'create', 'store', 'login', 'find', 'detail']);
    }

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Painter::withTrashed()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.painter.entry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\PainterEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PainterEntryRequest $request)
    {
        $painter = new Painter();

        $painter->email = $request->email;
        $painter->password = bcrypt($request->password);
        $painter->name = $request->name;
        $painter->message_key = md5(uniqid(rand(), true));  // チャットメッセージの発信元を特定するキー

        $painter->save();

        return redirect()->route('painter.complete');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $painter = Painter::find($this->guard()->id());
        $examples = $painter->examples;
        $columns = $painter->columns;

        return view(config('const.template.painter.mypage'), compact('painter', 'examples', 'columns'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view(config('const.template.painter.edit'), ['painter' => Painter::find($this->guard()->id())]);
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

        // プロフィール画像保存場所
        $dir = config('const.directory.profile_p');

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
            $del_flg = filled($painter->image_file);

            $filename = $dir . $filename;
        } else if (filled($painter->image_file)) {
            // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
            $del_flg = $request->boolean('del_flg');
        }

        if ($del_flg) {
            // 古い画像ファイルを削除
            unlink(storage_path('app/public/' . $painter->image_file));
        } else {
            // ファイル名変更無
            if (filled($painter->image_file)) {
                $filename = $painter->image_file;
            }
        }

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
        $painter->image_file = $filename;
        $painter->catch_copy = $request->catch_copy;
        $painter->constructions = $request->constructions;
        $painter->pr_copy = $request->pr_copy;

        $painter->save();

        return redirect()->route('painter.mypage');
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
                return redirect()->intended(route('painter.top'));
            } else {
                return back()->with('status', '認証に失敗しました。');
            }
        }

        return view(config('const.template.painter.login'));
    }

    /**
     * トップページ
     *
     * @return \Illuminate\Http\Response
     */
    public function top()
    {
        return view(config('const.template.painter.top'), ['painter' => Painter::find($this->guard()->id())]);
    }

    /**
     * ログアウト
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
                'password' => 'password:painter',
            ]);

            Painter::destroy($this->guard()->id());

            $this->guard()->logout();

            // return view(config('const.template.painter.withdrawed'));
            return redirect()->route('top');
        }

        return view(config('const.template.painter.withdraw'));
    }

    /**
     * 一般向け業者会員検索
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
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

        // １ページあたりの表示件数
        $num = $request->input('num', 20);

        // ランクの高い順に取得する
        // TODO:将来的にはマッチ度で並べ替えする
        $painter = $painter->orderby('rank', 'desc')->orderby('painter_id', 'asc')->paginate($num);

        // return $painter;
        return view(config('const.template.painter.list'), ['painter' => $painter]);
    }

    /**
     * 一般向け業者会員詳細ページ
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        return view(config('const.template.painter.detail'), ['painter' => Painter::find($id)]);
    }

    /**
     * チャット画面
     *
     * @param  int  $request_id
     * @return \Illuminate\Http\Response
     */
    public function chat($request_id)
    {
        $proposal = \App\Proposal::where([
            ['painter_id', $this->guard()->id()],
            ['request_id', $request_id],
        ])->first();

        $request_model = \App\Request::find($request_id);
        $user_id = $request_model->user_id;
        $painter_id = $this->guard()->id();
        $status = config('const.select.status')[$proposal->status];
        $painter = Painter::find($painter_id);
        $message_key = $painter->message_key;
        $url = '/painter/mypage';

        return view(config('const.template.chat'), compact('user_id', 'painter_id', 'request_id', 'status', 'message_key', 'url'));
    }

    /**
     * サイト外施工事例
     *
     * @return \Illuminate\Http\Response
     */
    public function exampleentry()
    {
        return view(config('const.template.painter.exampleentry'), ['painter' => Painter::find($this->guard()->id())]);
    }
}
