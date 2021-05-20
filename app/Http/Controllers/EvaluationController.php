<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EvaluationRequest;
use App\Evaluation;

class EvaluationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter')->except(['show', 'edit', 'update', 'list']);
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
        return Evaluation::where('painter_id', $this->guard()->id())->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param = [
            'painter_id' => $this->guard()->id(),
            'contract_id' => $request->contract_id
        ];

        Evaluation::firstOrNew($param)->save();

        return Evaluation::where($param)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Evaluation::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // return view(config('const.template.evaluation'), ['evaluation' => Evaluation::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EvaluationRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluationRequest $request, $id)
    {
        $evaluation = Evaluation::find($id);

        $evaluation->quality = $request->quality;
        $evaluation->price = $request->price;
        $evaluation->speed = $request->speed;
        $evaluation->correspondence = $request->correspondence;
        $evaluation->evaluation = $request->evaluation;
        $evaluation->flg = 1;

        $evaluation->save();

        // return Evaluation::where('user_id', $request->user_id)->get();
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
        Evaluation::destroy($id);

        // return Evaluation::where('painter_id', $this->guard()->id())->get();
        // return redirect()->route('painter.mypage');
    }

    /**
     * 口コミ・評価データ取得（一般公開用）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // 評価済データのみ取得
        $evaluation = Evaluation::where('flg', 1);

        if ($request->has('painter_id')) {
            $painter_id = $request->painter_id;

            // 業者IDが指定されていれば、指定の業者のデータのみ取得
            $evaluation = $evaluation->where('painter_id', $painter_id);
        }

        // 最新のデータから順に取得
        $evaluation = $evaluation->orderby('updated_at', 'desc')->get();

        // return $evaluation;
        // return view(config('const.template.evaluation.list'), ['evaluation' => $evaluation]);
    }
}
