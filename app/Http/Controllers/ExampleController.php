<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Http\Requests\ExampleRequest;
use App\Example;
use App\Property;
use App\Contract;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter')->except(['show', 'publish', 'list']);
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
        return Example::where('painter_id', $this->guard()->id())->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.example.create'));
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

        Example::firstOrNew($param)->save();

        return Example::where($param)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Example::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(config('const.template.example.edit'), ['example' => Example::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\ExampleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExampleRequest $request, $id)
    {
        $example = Example::find($id);

        // 施工事例画像保存場所
        $dir = config('const.directory.example');

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
                $del_flg = filled($example->value($field));

                $filename = $dir . $filename;
            } else if (filled($example->value($field))) {
                // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
                $del_flg = $request->boolean('del_flg' . $i);
            }

            if ($del_flg) {
                // 古い画像ファイルを削除
                unlink(storage_path('app/public/' . $example->value($field)));
            } else {
                // ファイル名変更無
                if (filled($example->value($field))) {
                    $filename = $example->value($field);
                }
            }

            $example->fill([$field => $filename]);
        }

        $example->comment = $request->comment;

        // サイト外施工事例の場合は公開状態も変更可能
        if ($request->filled('public_consent')) {
            $example->public_consent = $request->public_consent;
        }

        $example->save();

        // return Example::where('painter_id', $this->guard()->id())->get();
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
        Example::destroy($id);

        // return Example::where('painter_id', $this->guard()->id())->get();
        return redirect()->route('painter.mypage');
    }

    /**
     * サイト外施工事例新規作成処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function example(Request $request)
    {
        // バリデーション
        $request->validate([
            'name'           => 'nullable|string|max:48',
            'address'        => 'nullable|string|max:256',
            'area'           => 'nullable|numeric|max:9999',
            'area_b'         => 'nullable|numeric|max:9999',
            'floors'         => 'nullable|numeric|max:99',
            'age'            => 'nullable|numeric|max:99',
            'type'           => 'nullable|numeric',
            'type_wall'      => 'nullable|numeric',
            'type_roof'      => 'nullable|numeric',
            'category'       => 'nullable|numeric',
            'plan'           => 'nullable|string|max:256',
            'period'         => 'nullable|numeric|max:999',
            'paint'          => 'nullable|string|max:256',
            'memo'           => 'nullable|string',
            'complete_date'  => 'nullable|date',
            'amount'         => 'nullable|numeric|max:99999999', // 円単位
            'image_file1'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file2'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file3'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file4'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file5'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'image_file6'    => "nullable|file|image|max:{$max_size}|mimes:jpg,jpeg,gif,png",
            'comment'        => 'nullable|string',
            'public_consent' => 'nullable|boolean',
        ]);

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
            'amount' => $request->amount
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
        $example->public_consent = $request->public_consent;

        $example->save();

        // return Example::where('painter_id', $this->guard()->id())->get();
        return redirect()->route('painter.mypage');
    }

    /**
     * 公開承諾処理
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publish($id)
    {
        $example = Example::find($id);

        // 公開承諾フラグに1（承諾済）を設定する
        $example->public_consent = 1;

        $example->save();

        return $example;
    }

    /**
     * 施工事例データ取得（一般公開用）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
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

        // 最新のデータから順に取得
        $example = $example->orderby('updated_at', 'desc')->get();

        // return $example;
        return view(config('const.template.example.list'), ['example' => $example]);
    }
}
