<?php

namespace App\Http\Controllers\Api\Stub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Http\Requests\ColumnRequest;
use App\Column;

class ColumnController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:painter')->except(['show', 'list']);
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
        return Column::where('painter_id', $this->guard()->id())->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(config('const.template.column.create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\ColumnRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColumnRequest $request)
    {
        $column = new Column();

        $column->painter_id = $this->guard()->id();
        $column->title = $request->title;
        $column->category = $request->category;

        // コラム画像保存場所
        $dir = config('const.directory.column');

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

            $column->fill([$field => $filename]);
        }

        $column->contents = $request->contents;
        $column->public = $request->public;

        $column->save();

        // return Column::where('painter_id', $this->guard()->id())->get();
        // return redirect()->route('painter.mypage');
        return response()->json([
            'next' => route('painter.mypage')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Column::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(config('const.template.column.edit'), ['column' => Column::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\ColumnRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ColumnRequest $request, $id)
    {
        $column = Column::find($id);

        $column->title = $request->title;
        $column->category = $request->category;

        // コラム画像保存場所
        $dir = config('const.directory.column');

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
                $del_flg = filled($column->value($field));

                $filename = $dir . $filename;
            } else if (filled($column->value($field))) {
                // 登録済みの画像ファイルがある場合、明示的に削除指定されていれば削除する
                $del_flg = $request->boolean('del_flg' . $i);
            }

            if ($del_flg) {
                // 古い画像ファイルを削除
                unlink(storage_path('app/public/' . $column->value($field)));
            } else {
                // ファイル名変更無
                if (filled($column->value($field))) {
                    $filename = $column->value($field);
                }
            }

            $column->fill([$field => $filename]);
        }

        $column->contents = $request->contents;
        $column->public = $request->public;

        $column->save();

        // return Column::where('painter_id', $this->guard()->id())->get();
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
        Column::destroy($id);

        // return Column::where('painter_id', $this->guard()->id())->get();
        return redirect()->route('painter.mypage');
    }

    /**
     * コラムデータ取得（一般公開用）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // 公開データのみ取得
        $column = Column::where('public', 1);

        if ($request->has('painter_id')) {
            $painter_id = $request->painter_id;

            // 業者IDが指定されていれば、指定の業者のデータのみ取得
            if (is_array($painter_id)) {
                $column = $column->whereIn('painter_id', $painter_id);
            } else {
                $column = $column->where('painter_id', $painter_id);
            }
        }

        // 最新のデータから順に取得
        $column = $column->orderby('updated_at', 'desc')->get();

        // return $column;
        return view(config('const.template.column.list'), ['column' => $column]);
    }
}
