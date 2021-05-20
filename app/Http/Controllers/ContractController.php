<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ContractRequest;
use App\Contract;
use App\Proposal;

class ContractController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter');
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
        return Contract::where('painter_id', $this->guard()->id())->get();
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
            'user_id' => $request->user_id,
            'property_id' => $request->property_id,
            'request_id' => $request->request_id,
            'painter_id' => $this->guard()->id()
        ];

        Contract::firstOrNew($param)->save();

        return Contract::where($param)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Contract::find($id);
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
     * @param  \App\Http\Requests\ContractRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, $id)
    {
        $contract = Contract::find($id);

        $contract->category = $request->category;

        // 契約書保存場所
        $dir = config('const.directory.contract');

        $filename = '';
        $del_flg = false;

        // PDFファイル保存処理
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $filename = md5(uniqid(rand(), true)) . '.pdf';

            $request->file('document')->storeAs('public/' . $dir, $filename);

            // 新しいPDFファイルがアップロードされた場合、登録済みのPDFファイルがあれば削除する
            $del_flg = filled($contract->document);

            $filename = $dir . $filename;
        } else if (filled($contract->document)) {
            // 登録済みのPDFファイルがある場合、明示的に削除指定されていれば削除する
            $del_flg = $request->boolean('del_flg');
        }

        if ($del_flg) {
            // 古いPDFファイルを削除
            unlink(storage_path('app/public/' . $contract->document));
        } else {
            // ファイル名変更無
            if (filled($contract->document)) {
                $filename = $contract->document;
            }
        }

        $contract->contract_amount = $request->contract_amount;
        $contract->contract_date = $request->contract_date;
        $contract->contract_details = $request->contract_details;
        $contract->charge_name = $request->charge_name;
        $contract->plan = $request->plan;
        $contract->period = $request->period;
        $contract->paint = $request->paint;
        $contract->memo = $request->memo;

        $contract->save();

        $proposal = Proposal::where([
            ['painter_id', $this->guard()->id()],
            ['request_id', $contract->request_id]
        ])->first();

        $proposal->status = 4;  // 本契約

        $proposal->save();

        // return Contract::where('painter_id', $this->guard()->id())->get();
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
        Contract::destroy($id);

        // return Contract::where('painter_id', $this->guard()->id())->get();
        return redirect()->route('painter.mypage');
    }
}
