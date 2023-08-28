<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Models\Admin;

class UserMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::guard('web')->user()->id;
        $messages = AdminMessage::withTrashed()
                                ->where('user_id', $userId)
                                ->where('text', '!=', null)
                                ->where('hidden', '==', 0)
                                ->orderByDesc('id')->get();
        $admins = Admin::all();
        return view('users.messages.index', compact('messages', 'admins'));
    }

    /**
     * 下書き一覧
     */
    public function draft()
    {
        $userId = Auth::guard('web')->user()->id;
        $messages = UserMessage::where('user_id', $userId)->where('draft', '!=', null)->orderByDesc('id')->get();
        $admins = Admin::all();
        return view('users.messages.draftIndex', compact('messages', 'admins'));
    }

    /**
     * 送信済み一覧
     */
    public function sent()
    {
        $userId = Auth::guard('web')->user()->id;
        $messages = UserMessage::where('user_id', $userId)->where('text', '!=', null)->orderByDesc('id')->get();
        $admins = Admin::all();
        return view('users.messages.sentIndex', compact('messages', 'admins'));
    }

    /**
     * ゴミ箱
     */
    public function dust()
    {
        $messages = UserMessage::all();
        return view('users.messages.dust', compact('messages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admins = Admin::all();
        return view('users.messages.create', compact('admins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
            UserMessage::create($data);
            return redirect()->route('user.message.index')->with('message', 'メッセージを送信しました');

        } else {
            $data['draft'] = $request->text;
            UserMessage::create($data);
            return redirect()->route('user.message.index')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminMessage $message)
    {
        $admins = Admin::all();

        return view('users.messages.show', compact('message', 'admins'));
    }

    /**
     * 送信済みShow
     */
    public function sentShow(UserMessage $message)
    {
        $admins = Admin::all();
        $user = 1;
        return view('users.messages.show', compact('message', 'admins', 'user'));

    }

    /**
     * 返信画面
     */
    public function reply(AdminMessage $message)
    {
        $admin = Admin::where('id' , $message->admin_id)->first();
        return view('users.messages.reply', compact('message', 'admin'));
    }

    /**
     * 返信登録
     */
    public function replyStore(StoreMessageRequest $request, $message)
    {
        $userId = Auth::guard('web')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
        } else {
            $data['draft'] = $request->text;
        }

        UserMessage::create($data);

        return redirect()->route('user.message.show', compact('message'))->with('message', 'メッセージを送信しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMessage $message)
    {
        $admins = Admin::all();
        return view('users.messages.edit', compact('message', 'admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, UserMessage $message)
    {
        $userId = Auth::guard('web')->user()->id;
        $action = $request->input('action');

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
        ];

        if ($action == '送信') {
            $data['text']  = $request->text;
            $data['draft'] = null;
            $message->update($data);
            return redirect()->route('user.message.index')->with('message', 'メッセージを送信しました');

        } else {
            $data['draft'] = $request->text;
            $data['text']  = null;
            $message->update($data);
            return redirect()->route('user.message.index')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMessage $message)
    {
        $message->delete();
        return Redirect::back()->with('danger', $message->title . 'を削除しました');
    }

    /**
     * 非表示
     */
    public function hidden(AdminMessage $message)
    {
        $hidden = UserMessage::find($message->id);
        $hidden->update(['hidden' => 1]);
        return redirect()->route('user.message.index')->with('danger', $message->title . 'を削除しました');
    }
}
