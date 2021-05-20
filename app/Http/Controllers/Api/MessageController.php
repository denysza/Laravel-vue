<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use \Image;
use App\Http\Middleware\ImageFilter;
use App\Message;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $message = new Message();

        $message->user_id = $request->user_id;
        $message->painter_id = $request->painter_id;
        $message->request_id = $request->request_id;
        $message->message_key = $request->message_key;

        if ($request->hasFile('image')) {
            // ディレクトリがない場合は作成する
            $dname = storage_path('app/chat/image/');

            if (!file_exists($dname)) {
                mkdir($dname, 0777, true);
            }

            // リサイズ画像を保存
            $filename = md5(uniqid(rand(), true)) . '.jpg';
            $file = $request->image;

            Image::make($file)->filter(new ImageFilter($file->getClientMimeType()))->save($dname . $filename);

            $message->type = 'IMG';
            $message->contents = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dname . $filename));

            unlink($dname . $filename);
        } else if ($request->hasFile('pdf')) {
            $filename = md5(uniqid(rand(), true));
            $request->pdf->storeAs('chat/pdf/', $filename . '.pdf');

            $message->type = 'PDF';
            $message->contents = '/api/pdf/' . $filename;
        } else {
            $message->type = 'TXT';
            $message->contents = $request->contents;
        }

        $message->save();

        $id = $request->request_id . '-' . $request->painter_id;

        // ブロードキャスト送信イベント
        event(new MessageSent($id, $message->message_key));
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
     * メッセージデータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $message = Message::join('painters', 'messages.painter_id', '=', 'painters.id')
                   ->join('users', 'messages.user_id', '=', 'users.id')
                   ->select('messages.id', 'messages.type', 'messages.contents')
                   ->selectRaw('case when painters.message_key = messages.message_key then painters.name else users.nickname end as name')
                   ->selectRaw('case when painters.message_key = messages.message_key then painters.image_file else users.image_file end as profile')
                   ->selectRaw('messages.message_key = ? as flg', [$request->message_key])
                   ->selectRaw('case when datediff(current_date(), messages.created_at) = 0 then "今日" when datediff(current_date(), messages.created_at) = 1 then "昨日" else date_format(messages.created_at, "%c/%e") end as m_date')
                   ->selectRaw('time_format(messages.created_at, "%k:%i") as m_time')
                   ->where('messages.user_id', $request->user_id)
                   ->where('messages.painter_id', $request->painter_id)
                   ->where('messages.request_id', $request->request_id)
                   ->orderBy('messages.id', 'asc')->get();

        return $message;
    }

    /**
     * PDFファイルをBase64エンコードして返却
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function pdf($name)
    {
        return 'data:application/pdf;base64,' . base64_encode(file_get_contents(storage_path('app/chat/pdf/') . $name . '.pdf'));
    }

    /**
     * メッセージデータを全消去
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clear(Request $request)
    {
        // PDFファイルを削除
        $messages = Message::where([
            ['user_id', $request->user_id],
            ['painter_id', $request->painter_id],
            ['request_id', $request->request_id],
            ['type', 'PDF']
        ])->get();

        $dname = storage_path('app/chat/pdf/');

        foreach ($messages as $message) {
            $filename = str_replace('/api/pdf/', '', $message->contents) . '.pdf';
            unlink($dname . $filename);
        }

        // 削除処理
        Message::where([
            ['user_id', $request->user_id],
            ['painter_id', $request->painter_id],
            ['request_id', $request->request_id]
        ])->delete();
    }
}
