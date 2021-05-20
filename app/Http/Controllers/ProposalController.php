<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProposalRequest;
use App\Proposal;

class ProposalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter')->except(['store', 'show']);
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
        //
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
            'painter_id' => $request->painter_id,
            'request_id' => $request->request_id
        ];

        Proposal::firstOrNew($param)->save();

        // TODO:対象業者宛てメール送信処理

        // TODO:対象業者宛てチャットの初期メッセージ送信

        return Proposal::where($param)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Proposal::find($id);
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
     * @param  \App\Http\Requests\ProposalRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProposalRequest $request, $id)
    {
        $proposal = Proposal::find($id);

        if ($request->filled('visit_schedule')) {
            $proposal->visit_schedule = $request->visit_schedule;
        }

        if ($request->filled('visit_record') && !filled($proposal->visit_record)) {
            $proposal->visit_record = $request->visit_record;
        }

        // 見積書保存場所
        $dir = config('const.directory.quotation');

        // 見積書保存処理
        for ($i = 1; $i <= 5; $i++) {
            $filename = '';
            $del_flg = false;
            $field = 'quotation' . $i;

            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $filename = md5(uniqid(rand(), true)) . '.pdf';

                $request->file($field)->storeAs('public/' . $dir, $filename);

                // 新しい見積書ファイルがアップロードされた場合、登録済みの見積書ファイルがあれば削除する
                $del_flg = filled($proposal->value($field));

                $filename = $dir . $filename;
            } else if ($request->filled('del_flg_q' . $i) && filled($proposal->value($field))) {
                // 登録済みの見積書ファイルがある場合、明示的に削除指定されていれば削除する
                $del_flg = $request->boolean('del_flg_q' . $i);
            }

            if ($del_flg) {
                // 古い見積書ファイルを削除
                unlink(storage_path('app/public/' . $proposal->value($field)));
            } else {
                // ファイル名変更無
                if (filled($proposal->value($field))) {
                    $filename = $proposal->value($field);
                }
            }

            $proposal->fill([$field => $filename]);
        }

        if ($proposal->status == 2) {
            $flg = false;

            for ($i = 1; $i <= 5; $i++) {
                if (filled($proposal->value('quotation' . $i))) {
                    // 見積提出
                    $flg = true;
                    break;
                }
            }

            if ($flg) {
                $proposal->status = 3;  // 見積提出
            }
        }

        // 添付書類保存場所
        $dir = config('const.directory.attach');

        // 添付書類保存処理
        for ($i = 1; $i <= 5; $i++) {
            $filename = '';
            $del_flg = false;
            $field = 'document' . $i;

            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $filename = md5(uniqid(rand(), true)) . '.pdf';

                $request->file($field)->storeAs('public/' . $dir, $filename);

                // 新しい添付書類ファイルがアップロードされた場合、登録済みの添付書類ファイルがあれば削除する
                $del_flg = filled($proposal->value($field));

                $filename = $dir . $filename;
            } else if ($request->filled('del_flg_a' . $i) && filled($proposal->value($field))) {
                // 登録済みの添付書類ファイルがある場合、明示的に削除指定されていれば削除する
                $del_flg = $request->boolean('del_flg_a' . $i);
            }

            if ($del_flg) {
                // 古い添付書類ファイルを削除
                unlink(storage_path('app/public/' . $proposal->value($field)));
            } else {
                // ファイル名変更無
                if (filled($proposal->value($field))) {
                    $filename = $proposal->value($field);
                }
            }

            $proposal->fill([$field => $filename]);
        }

        if ($request->filled('user_memo')) {
            $proposal->user_memo = $request->user_memo;
        }

        if ($request->filled('painter_memo')) {
            $proposal->painter_memo = $request->painter_memo;
        }

        if ($request->filled('visit_memo')) {
            $proposal->visit_memo = $request->visit_memo;
        }

        if ($request->filled('quotation_memo')) {
            $proposal->quotation_memo = $request->quotation_memo;
        }

        $proposal->save();

        // return Proposal::where('painter_id', $this->guard()->id())->get();
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
        Proposal::destroy($id);

        // return Proposal::where('painter_id', $this->guard()->id())->get();
        return redirect()->route('painter.mypage');
    }
}
