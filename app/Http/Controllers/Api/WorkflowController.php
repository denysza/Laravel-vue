<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Contract;
use App\Evaluation;
use App\Example;
use App\Favorite;
use App\Message;
use App\Painter;
use App\Property;
use App\Proposal;
use App\Request as RequestModel;
use App\User;
use App\Mail\Estimate;

class WorkflowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user')->only(['estimate', 'workflow_u', 'negotiation', 'abandon_req', 'contract', 'delete_data']);
        $this->middleware('auth:painter')->only(['proposal', 'workflow_p', 'abandon_prop', 'finish', 'complete', 'delete_data']);
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
        //
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
        //
    }

    /**
     * 見積依頼入力
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function estimate(Request $request)
    {
        $user_id = Auth::guard('user')->id();

        if ($request->isMethod('put')) {
            // バリデーション
            $request->validate([
                'area'      => 'nullable|numeric|max:9999',
                'area_b'    => 'nullable|numeric|max:9999',
                'floors'    => 'nullable|numeric|max:99',
                'age'       => 'nullable|numeric|max:99',
                'num'       => 'nullable|numeric|max:999',
                'type'      => 'nullable|numeric',
                'type_wall' => 'nullable|numeric',
                'type_roof' => 'nullable|numeric',
                'category'  => 'nullable|numeric',
                'budget'    => 'nullable|numeric|max:9999', // 万円単位
            ]);

            // 確認ページ用データ作成
            $data = [];

            $data['property_id'] = $request->property_id;

            $data['category'] = $request->category;
            $data['category_text'] = config('const.select.category')[$request->category];

            if (is_array($request->priority)) {
                $priority = [];

                foreach ($request->priority as $key => $val) {
                    $priority[] = config('const.select.priority')[$val];
                }

                $data['priority'] = implode(',', $request->priority);
                $data['priority_text'] = implode("\n", $priority);
            } else {
                $data['priority'] = $request->priority;
                $data['priority_text'] = config('const.select.priority')[$request->priority];
            }

            $data['period'] = $request->period;
            $data['period_text'] = config('const.select.period')[$request->period];

            $data['type'] = $request->type;
            $data['num'] = $request->num;

            if ($request->filled('num')) {
                $data['property'] = config('const.select.property')[$request->type] . '　' . $request->num . '戸';
            } else {
                $data['property'] = config('const.select.property')[$request->type];
            }

            $data['floors'] = $request->floors;
            $data['floors_text'] = $request->floors . '階';

            $data['age'] = $request->age;
            $data['age_text'] = '築' . $request->age . '年';

            $data['area'] = $request->area;
            $data['area_text'] = '敷地面積　' . $request->area . '㎡';

            $data['area_b'] = $request->area_b;
            $data['area_b_text'] = '建坪　' . $request->area_b . '㎡';

            $data['type_roof'] = $request->type_roof;
            $data['type_roof_text'] = '屋根形状　' . config('const.select.roof')[$request->type_roof];

            $data['type_wall'] = $request->type_wall;
            $data['type_wall_text'] = '外壁　' . config('const.select.wall')[$request->type_wall];

            $data['budget'] = $request->budget;
            $data['budget_text'] = '予算　' . $request->budget . '万円';

            $data['memo'] = $request->memo;

            $painters = Painter::whereIn('id', $request->painter_id)->pluck('name')->toArray();

            $data['painter_id'] = $request->painter_id;
            $data['painters'] = implode("\n", $painters);

            return view(config('const.template.estimate_conf'), ['data' => $data]);
        } else if ($request->isMethod('post')) {
            // 物件テーブルの更新
            $property = Property::find($request->property_id);

            $property->area = $request->area;
            $property->area_b = $request->area_b;
            $property->floors = $request->floors;
            $property->age = $request->age;
            $property->type = $request->type;

            if ($request->filled('num')) {
                $property->num = $request->num;
            }

            $property->type_wall = $request->type_wall;
            $property->type_roof = $request->type_roof;

            $property->save();

            // 依頼テーブルの検索又は新規作成
            $user = User::find($user_id);

            $param = [
                'user_id' => $user->id,
                'property_id' => $property->id
            ];

            $request_model = RequestModel::firstOrNew($param);

            $request_model->budget = $request->budget;
            $request_model->category = $request->category;
            $request_model->priority = $request->priority;
            $request_model->period = $request->period;
            $request_model->memo = $request->memo;

            $request_model->save();

            // 依頼テーブルの再取得
            $request_model = RequestModel::where($param)->first();
            $request_id = $request_model->id;

            // メール送信パラメータ作成
            $data = [];

            $data['category'] = $request->category_text;
            $data['priority'] = $request->priority_text;
            $data['period'] = $request->period_text;
            $data['property'] = $request->property;
            $data['floors'] = $request->floors_text;
            $data['age'] = $request->age_text;
            $data['area'] = $request->area_text;
            $data['area_b'] = $request->area_b_text;
            $data['type_roof'] = $request->type_roof_text;
            $data['type_wall'] = $request->type_wall_text;
            $data['budget'] = $request->budget_text;
            $data['memo'] = $request->memo;

            // 個人会員宛メール送信処理
            $data['type'] = 'user';
            $data['subject'] = '一括見積をご利用いただきありがとうございます';

            // Mail::to($user->email)->send(new Estimate($data));

            // 業者会員宛メール送信処理
            $data['type'] = 'painter';
            $data['subject'] = '一括見積ご利用通知';

            if (is_array($request->painter_id)) {
                foreach ($request->painter_id as $key => $val) {
                    if (Proposal::where('painter_id', $val)->where('request_id', $request_id)->doesntExist()) {
                        // 提案テーブルに初期データ登録
                        $proposal = new Proposal();

                        $proposal->painter_id = $val;
                        $proposal->request_id = $request_id;
                        $proposal->bulk_flg = 1; // 一括

                        $proposal->save();

                        // メール送信処理
                        $painter = Painter::find($val);

                        // Mail::to($painter->email)->send(new Estimate($data));
                    }
                }
            } else {
                if (Proposal::where('painter_id', $request->painter_id)->where('request_id', $request_id)->doesntExist()) {
                    // 提案テーブルに初期データ登録
                    $proposal = new Proposal();

                    $proposal->painter_id = $request->painter_id;
                    $proposal->request_id = $request_id;
                    $proposal->bulk_flg = 0; // 個別

                    $proposal->save();

                    // メール送信処理
                    $painter = Painter::find($request->painter_id);

                    // Mail::to($painter->email)->send(new Estimate($data));
                }
            }

            return redirect()->route('user.mypage');
        }

        return view(config('const.template.estimate'), ['user_id' => $user_id]);
    }

    /**
     * 物件情報取得
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function get_properties($user_id)
    {
        return Property::where('user_id', $user_id)->get();
    }

    /**
     * お気に入り業者情報取得
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function get_favorites($user_id)
    {
        return Favorite::join('painters', 'favorites.painter_id', '=', 'painters.id')->select('favorites.*', 'painters.name')->where('favorites.user_id', $user_id)->orderby('favorites.painter_id', 'asc')->get();
    }

    /**
     * 見積応答入力
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function proposal(Request $request)
    {
        if ($request->isMethod('post')) {
            // 提案テーブルのステータス更新
            $proposal = Proposal::find($request->proposal_id);

            $proposal->status = 1; // 相談中

            $proposal->save();

            $painter = Painter::find($proposal->painter_id);
            $request_model = RequestModel::find($proposal->request_id);

            // メッセージテーブルに初期メッセージ登録
            $message = new Message();

            $message->user_id = $request_model->user_id;
            $message->painter_id = $painter->id;
            $message->request_id = $request_model->id;
            $message->message_key = $painter->message_key;
            $message->type = 'TXT';
            $message->contents = config('const.message');

            $message->save();

            // return redirect('/api/proposal');
        }

        return view(config('const.template.proposal'), ['proposal' => Proposal::where('painter_id', Auth::guard('painter')->id())->where('status', 0)->get()]);
    }

    /**
     * 相談・商談一覧（個人）
     *
     * @return \Illuminate\Http\Response
     */
    public function workflow_u()
    {
        $user_id = Auth::guard('user')->id();

        // 依頼データ
        $request_models = RequestModel::where('user_id', $user_id)->orderby('id', 'asc')->get();

        $request_id = [];

        foreach ($request_models as $request_model) {
            $request_id[] = $request_model->id;
        }

        // 相談中データ
        $proposal_r = Proposal::whereIn('request_id', $request_id)
                      ->where('status', 1)
                      ->orderby('request_id', 'asc')
                      ->orderby('painter_id', 'asc')
                      ->get();

        // 商談中データ
        $proposal_n = Proposal::leftJoin('contracts', function($join) {
                          $join->on('proposals.request_id', '=', 'contracts.request_id');
                          $join->on('proposals.painter_id', '=', 'contracts.painter_id');
                      })
                      ->select('proposals.*')
                      ->selectRaw('case when contracts.id is null then 0 else contracts.id end as contract_id')
                      ->whereIn('proposals.request_id', $request_id)
                      ->where('proposals.status', '>=', 2)
                      ->orderby('proposals.request_id', 'asc')
                      ->orderby('proposals.painter_id', 'asc')
                      ->get();

        $url = '/user/mypage';

//        return view(config('const.template.user.workflow'), compact('request_models', 'proposal_r', 'proposal_n'));
        return view(config('const.template.user.workflow'), ['user_id' => $user_id, 'url' => '/user/mypage']);
    }

    /**
     * 相談・商談一覧（業者）
     *
     * @return \Illuminate\Http\Response
     */
    public function workflow_p()
    {
        $painter_id = Auth::guard('painter')->id();

        $proposals = Proposal::where('painter_id', $painter_id)->get();

        $request_id = [];

        foreach ($proposals as $proposal) {
            $request_id[] = $proposal->request_id;
        }

        // 依頼データ
        $request_models = RequestModel::whereIn('id', $request_id)->orderby('id', 'asc')->get();

        // 相談中データ
        $proposal_r = Proposal::where('painter_id', $painter_id)
                      ->where('status', 1)
                      ->orderby('request_id', 'asc')
                      ->get();

        // 商談中データ
        $proposal_n = Proposal::leftJoin('contracts', function($join) {
                          $join->on('proposals.request_id', '=', 'contracts.request_id');
                          $join->on('proposals.painter_id', '=', 'contracts.painter_id');
                      })
                      ->select('proposals.*')
                      ->selectRaw('case when contracts.id is null then 0 else contracts.id end as contract_id')
                      ->where('proposals.painter_id', $painter_id)
                      ->where('proposals.status', '>=', 2)
                      ->orderby('proposals.request_id', 'asc')
                      ->get();

//        return view(config('const.template.painter.workflow'), compact('request_models', 'proposal_r', 'proposal_n'));
        return view(config('const.template.painter.workflow'), ['user_id' => 0, 'url' => '/painter/mypage']);
    }

    /**
     * 相談・商談一覧（チャットメッセージ取得）
     *
     * @param  int  $id
     * @param  int  $kbn
     * @return \Illuminate\Http\Response
     */
    public function get_messages($id, $kbn)
    {
        $messages = Message::selectRaw('max(id) as id_max, request_id, painter_id')->where('deleted_at', null)->groupbyRaw('request_id, painter_id');

        if ($id > 0) {
            // 個人会員
            $query = Message::join('proposals',  function($join) {
                          $join->on('messages.request_id', '=', 'proposals.request_id');
                          $join->on('messages.painter_id', '=', 'proposals.painter_id');
                     })
                     ->join('painters', 'messages.painter_id', '=', 'painters.id')
                     ->joinSub($messages, 'message_max', function($join) {
                          $join->on('messages.id', '=', 'message_max.id_max');
                          $join->on('messages.request_id', '=', 'message_max.request_id');
                          $join->on('messages.painter_id', '=', 'message_max.painter_id');
                     })
                     ->select('messages.contents', 'messages.painter_id', 'messages.request_id', 'painters.name')
                     ->where('messages.type', 'TXT')
                     ->where('messages.request_id', $id)
                     ->distinct();
        } else {
            // 業者会員
            $query = Message::join('proposals',  function($join) {
                          $join->on('messages.request_id', '=', 'proposals.request_id');
                          $join->on('messages.painter_id', '=', 'proposals.painter_id');
                     })
                     ->join('users', 'messages.user_id', '=', 'users.id')
                     ->joinSub($messages, 'message_max', function($join) {
                          $join->on('messages.id', '=', 'message_max.id_max');
                          $join->on('messages.request_id', '=', 'message_max.request_id');
                          $join->on('messages.painter_id', '=', 'message_max.painter_id');
                     })
                     ->select('messages.contents', 'messages.painter_id', 'messages.request_id')
                     ->selectRaw("concat((case when proposals.status = 1 then coalesce(users.nickname, concat('ユーザー', users.id)) else concat(users.name1, ' ', users.name2) end), '様') as name")
                     ->where('messages.type', 'TXT')
                     ->where('messages.painter_id', Auth::guard('painter')->id())
                     ->distinct();
        }

        if ($kbn == 0) {
            // 相談中データ
            return $query->where('proposals.status', 1)->orderby('messages.request_id', 'asc')->orderby('messages.painter_id', 'asc')->get();
        } else {
            // 商談中データ
            return $query->where('proposals.status', '>=', 2)->orderby('messages.request_id', 'asc')->orderby('messages.painter_id', 'asc')->get();
        }
    }

    /**
     * 商談開始
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function negotiation($id)
    {
        $proposal = Proposal::find($id);

        $proposal->status = 2;  // 商談開始

        $proposal->save();

        return redirect('/api/workflow/user');
    }

    /**
     * 個別の相談・商談、又は依頼の取り下げ（個人）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function abandon_req(Request $request)
    {
        $proposal_id = [];

        if ($request->boolean('all')) {
            // 依頼データ削除
            RequestModel::destroy($request->request_id);

            $painter_id = [];

            $proposals = Proposal::where('request_id', $request->request_id)->get();

            foreach ($proposals as $proposal) {
                $painter_id[] = $proposal->painter_id;
                $proposal_id[] = $proposal->id;
            }
        } else {
            if (is_array($request->painter_id)) {
                $painter_id = $request->painter_id;
            } else {
                $painter_id = array($request->painter_id);
            }

            $proposals = Proposal::whereIn('painter_id', $painter_id)->where('request_id', $request->request_id)->get();

            foreach ($proposals as $proposal) {
                $proposal_id[] = $proposal->id;
            }
        }

        $this->delete_data($request->request_id, $painter_id, $proposal_id);

        // TODO:業者宛取下通知メール送信

        return Proposal::where('request_id', $request->request_id)->orderby('painter_id', 'asc')->get();
    }

    /**
     * 個別の相談・商談の取り下げ（業者）
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function abandon_prop($id)
    {
        $proposal = Proposal::find($id);

        $this->delete_data($proposal->request_id, array($proposal->painter_id), array($id));

        return Proposal::where('painter_id', $proposal->painter_id)->orderby('request_id', 'asc')->get();
    }

    /**
     * 契約処理
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contract($id)
    {
        $proposal = Proposal::find($id);
        $request_model = RequestModel::find($proposal->request_id);

        $param = [
            'user_id' => $request_model->user_id,
            'property_id' => $request_model->property_id,
            'request_id' => $request_model->id,
            'painter_id' => $proposal->painter_id
        ];

        Contract::firstOrNew($param)->save();

        // 他の業者がメッセージで送信したPDFファイルを削除
        $messages = Message::where('user_id', $request_model->user_id)
                    ->where('painter_id', '<>', $proposal->painter_id)
                    ->where('request_id', $request_model->id)
                    ->where('type', 'PDF')
                    ->get();

        $dname = storage_path('app/chat/pdf/');

        foreach ($messages as $message) {
            $filename = str_replace('/api/pdf/', '', $message->contents) . '.pdf';
            unlink($dname . $filename);
        }

        // 他の業者のメッセージ削除
        Message::where('user_id', $request_model->user_id)
               ->where('painter_id', '<>', $proposal->painter_id)
               ->where('request_id', $request_model->id)
               ->delete();

        // 他の業者宛契約済み通知メール送信
        $proposals = Proposal::where('painter_id', '<>', $proposal->painter_id)
                    ->where('request_id', $request_model->id)
                    ->get();

        foreach ($proposals as $proposal) {
            $painter = Painter::find($proposal->painter_id);

            // TODO:メール送信処理
            // Mail::to($painter->email)->send(new Estimate($data));

            // 他の業者の提案データ削除
            $proposal->delete();
        }

        // return Contract::where($param)->get();
        return redirect('/api/workflow/user');
    }

    /**
     * 工事終了
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function finish($id)
    {
        $proposal = Proposal::find($id);

        $proposal->status = 5;  // 工事終了

        $proposal->save();

        // return redirect()->route('painter.mypage');
        return redirect('/api/workflow/painter');
    }

    /**
     * 完工設定
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request)
    {
        // 契約データ更新
        $contract = Contract::find($request->contract_id);

        $contract->complete_date = $request->complete_date;
        $contract->amount = $request->amount;

        $contract->save();

        $painter_id = array($contract->painter_id);

        $proposal_id = Proposal::where('painter_id', $contract->painter_id)
                       ->where('request_id', $contract->request_id)
                       ->pluck('id')->toArray();

        $this->delete_data($contract->request_id, $painter_id, $proposal_id);

        // 評価データ新規作成
        $param = [
            'painter_id' => $contract->painter_id,
            'contract_id' => $contract->id
        ];

        Evaluation::firstOrNew($param)->save();

        // TODO:個人宛評価作成依頼メール送信

        // return Evaluation::where($param)->get();
    }

    /**
     * 提案データ及びメッセージデータ削除処理
     *
     * @param  int  $request_id
     * @param  int[]  $painter_id
     * @param  int[]  $proposal_id
     * @return \Illuminate\Http\Response
     */
    public function delete_data($request_id, $painter_id, $proposal_id)
    {
        $request_model = RequestModel::find($request_id);

        $user_id = $request_model->user_id;

        // 提案データ削除
        Proposal::whereIn('proposal_id', $proposal_id)->delete();

        // メッセージで送信したPDFファイルを削除
        $messages = Message::whereIn('painter_id', $painter_id)
                    ->where('user_id', $user_id)
                    ->where('request_id', $request_id)
                    ->where('type', 'PDF')
                    ->get();

        $dname = storage_path('app/chat/pdf/');

        foreach ($messages as $message) {
            $filename = str_replace('/api/pdf/', '', $message->contents) . '.pdf';
            unlink($dname . $filename);
        }

        // メッセージ削除
        Message::whereIn('painter_id', $painter_id)
               ->where('user_id', $user_id)
               ->where('request_id', $request_id)
               ->delete();
    }

    /**
     * 提案のステータスを任意の値に更新する（テストロジック）
     *
     * @param  int  $id
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */
    public function test($id, $status)
    {
        $proposal = Proposal::find($id);

        $proposal->status = $status;

        $proposal->save();

        return redirect('/api/workflow/painter');
    }
}
