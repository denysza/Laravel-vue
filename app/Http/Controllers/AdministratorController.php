<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdministratorRequest;
use App\Http\Requests\LoginAdminRequest;
use App\Administrator;
use App\Column;
use App\Contract;
use App\Evaluation;
use App\Example;
use App\Painter;
use App\Request as RequestModel;
use App\User;

class AdministratorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['create', 'store', 'login']);
    }

    /**
     * Get the guard.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Administrator::withTrashed()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.admin.entry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AdministratorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdministratorRequest $request)
    {
        $admin = new Administrator();

        $admin->username = $request->username;
        $admin->password = bcrypt($request->password);

        $admin->save();

        return redirect()->route('admin.list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Administrator::destroy($id);

        if ($id == $this->guard()->id()) {
            // ログイン中の管理者を削除した場合はログアウト
            $this->guard()->logout();

            return redirect()->route('top');
        }

        return Administrator::withTrashed()->get();
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
                'username' => 'required|string|max:256|alpha_dash',
                'password' => 'required|string|min:8|max:256|alpha_dash',
            ]);

            // 認証処理
            $credentials = $request->only('username', 'password');

            if ($this->guard()->attempt($credentials)) {
                return redirect()->intended(config('const.prefix.admin') . '/administrator_list');
            } else {
                return back()->with('status', '認証に失敗しました。');
            }
        }

        return view(config('const.template.admin.login'));
    }

    /**
     * ログアウトページ
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard()->logout();

        return redirect()->route('admin.login');
    }

    /**
     * 業者会員データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function painters()
    {
        return Painter::withTrashed()->get();
    }

    /**
     * 業者会員データ承認
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function painter_approve($id)
    {
        $painter = Painter::find($id);

        $painter->rank = 1;

        $painter->save();

        return Painter::withTrashed()->get();
    }

    /**
     * 業者会員データ削除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function painter_del($id)
    {
        Painter::destroy($id);

        return Painter::withTrashed()->get();
    }

    /**
     * 個人会員データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function users()
    {
        return User::withTrashed()->get();
    }

    /**
     * 個人会員データ削除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_del($id)
    {
        User::destroy($id);

        return User::withTrashed()->get();
    }

    /**
     * コラムデータ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function columns()
    {
        return $this->get_columns();
    }

    /**
     * コラムデータ削除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function column_del($id)
    {
        Column::destroy($id);

        return $this->get_columns();
    }

    /**
     * 施工事例データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function examples()
    {
        return $this->get_examples();
    }

    /**
     * 施工事例データ削除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function example_del($id)
    {
        Example::destroy($id);

        return $this->get_examples();
    }

    /**
     * 依頼データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function requests()
    {
        return view(config('const.template.admin.request_list'), ['requests' => RequestModel::withTrashed()->get()]);
    }

    /**
     * 契約データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function contracts()
    {
        return view(config('const.template.admin.contract_list'), ['contracts' => Contract::withTrashed()->get()]);
    }

    /**
     * 口コミ・評価データ全件取得
     *
     * @return \Illuminate\Http\Response
     */
    public function evaluations()
    {
        return Evaluation::withTrashed()->get();
    }

    public function get_columns()
    {
        $query = Column::withTrashed()
                       ->join('painters', 'columns.painter_id', '=', 'painters.id')
                       ->select('columns.*', 'painters.name')
                       ->orderBy('columns.painter_id', 'asc')
                       ->orderBy('columns.id', 'asc');

        return $query->get();
    }

    public function get_examples()
    {
        $query = Example::withTrashed()
                        ->join('painters', 'examples.painter_id', '=', 'painters.id')
                        ->join('contracts', function ($join) {
                            $join->on('examples.contract_id', '=', 'contracts.id')
                                 ->where('contracts.user_id', '0')
                                 ->where('contracts.request_id', '0');
                        })
                        ->join('properties', function ($join) {
                            $join->on('contracts.property_id', '=', 'properties.id')
                                 ->where('properties.user_id', '0');
                        })
                        ->select('examples.*', 'painters.name as painter_name', 'properties.name as property_name')
                        ->orderBy('examples.painter_id', 'asc')
                        ->orderBy('examples.id', 'asc');

        return $query->get();
    }

    public function get_evaluations()
    {
        $query = Evaluation::withTrashed();
                           ->join('painters', 'evaluations.painter_id', '=', 'painters.id')
                           ->join('contracts', 'evaluations.contract_id', '=', 'contracts.id')
                           ->join('users', 'contracts.user_id', '=', 'users.id')
                           ->select('evaluations.*', 'painters.name', 'users.name1', 'users.name2')
                           ->orderBy('evaluations.painter_id', 'asc')
                           ->orderBy('contracts.user_id', 'asc')
                           ->orderBy('evaluations.id', 'asc');

        return $query->get();
    }
}
