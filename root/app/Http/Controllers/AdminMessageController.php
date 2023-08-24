<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Models\User;

class AdminMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = UserMessage::where('admin_id', $adminId)->where('text', '!=', null)->get();
        $users = User::all();
        return view('admin.messages.index', compact('messages', 'users'));
    }

    /**
     * 下書き一覧
     */
    public function draft()
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = AdminMessage::where('admin_id', $adminId)->where('draft', '!=', null)->orderByDesc('id')->get();
        $users = User::all();
        return view('admin.messages.draftIndex', compact('messages', 'users'));
    }

    /**
     * 送信済み一覧
     */
    public function sent()
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = AdminMessage::where('admin_id', $adminId)->where('text', '!=', null)->orderByDesc('id')->get();
        $users = User::all();
        return view('admin.messages.sentIndex', compact('messages', 'users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.messages.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $adminId,
            'user_id'  => $request->user_id,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
            AdminMessage::create($data);
            return redirect()->route('admin.message.index')->with('message', 'メッセージを送信しました');
        } else {
            $data['draft'] = $request->text;
            AdminMessage::create($data);
            return redirect()->route('admin.message.index')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMessage $message)
    {
        $users = User::all();

        return view('admin.messages.show', compact('message', 'users'));
    }

    /**
     * 送信済みShow
     */
    public function sentShow(UserMessage $message)
    {
        $users = User::all();
        $admin = 1;
        return view('admin.messages.show', compact('message', 'admin', 'users'));

    }

    /**
     * 返信画面
     */
    public function reply(UserMessage $message)
    {
        $user = User::where('id' , $message->user_id)->first();
        return view('admin.messages.reply', compact('message', 'user'));
    }

    /**
     * 返信登録
     */
    public function replyStore(StoreMessageRequest $request, $message)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $adminId,
            'user_id'  => $request->user_id,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
        } else {
            $data['draft'] = $request->text;
        }

        AdminMessage::create($data);

        return redirect()->route('admin.message.show', compact('message'))->with('message', 'メッセージを送信しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminMessage $message)
    {
        $users = User::all();
        return view('admin.messages.edit', compact('message', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, AdminMessage $message)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $adminId,
            'user_id'  => $request->user_id,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
            $data['draft'] = null;
            $message->update($data);
            return redirect()->route('admin.message.index')->with('message', 'メッセージを送信しました');

        } else {
            $data['draft'] = $request->text;
            $data['text']  = null;
            $message->update($data);
            return redirect()->route('admin.message.index')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.message.index')->with('danger', $message->title . 'を削除しました');
    }
}
