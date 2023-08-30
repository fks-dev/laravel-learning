<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Models\User;

class AdminMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = UserMessage::withTrashed()
                                ->where('admin_id', $adminId)
                                ->where('action', '==', 1)
                                ->where('is_hidden', '==', 0)
                                ->orderByDesc('id')
                                ->paginate(10);
        $users = User::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.index', compact('messages', 'users'));
    }

    /**
     * 下書き一覧
     */
    public function draft(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = AdminMessage::where('admin_id', $adminId)
                                ->where('action', '==', 0)
                                ->orderByDesc('id')
                                ->paginate(10);
        $users = User::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.draftIndex', compact('messages', 'users'));
    }

    /**
     * 送信済み一覧
     */
    public function sent(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
        $messages = AdminMessage::where('admin_id', $adminId)
                                ->where('action', '==', 1)
                                ->orderByDesc('id')
                                ->paginate(10);
        $users = User::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.sentIndex', compact('messages', 'users'));
    }

    /**
     * ゴミ箱
     */
    public function dust()
    {
        $adminId  = Auth::guard('admin')->user()->id;
        $messages = AdminMessage::onlyTrashed()->where('admin_id', $adminId)->get();
        $userMessages  = UserMessage::where('is_hidden', 1)->where('admin_id', $adminId)->get();
        $combinedMessages = $messages->concat($userMessages)->sortByDesc('updated_at');
        return view('admin.messages.dust', compact('combinedMessages'));
    }

    // 復元
    public function restore($message)
    {
        $record = AdminMessage::withTrashed()->find($message);
        $record->restore();
        return redirect()->back()->with('success', 'メールを復元しました。');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.create', compact('users', 'currentPage'));
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
            'text'     => $request->text,
        ];

        if ($action == '送信') {
            $data['action'] = 1;
            AdminMessage::create($data);
            return redirect()->route('admin.message.index')->with('message', 'メッセージを送信しました');
        } else {
            $data['action'] = 0;
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
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.show', compact('message', 'users', 'currentPage'));
    }

    /**
     * 送信済みShow
     */
    public function sentShow(UserMessage $message)
    {
        $users = User::all();
        $admin = 1;
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.show', compact('message', 'admin', 'users', 'currentPage'));

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
            'text'     => $request->text,
        ];

        if ($action == '下書き') {
            $data['action'] = 2;
        } elseif ($action == '送信') {
            $data['action'] = 1;
            // 返信フラッグ
            $userMessage = UserMessage::find($message);
            $userMessage->is_replied = true;
            $userMessage->save();
        }

        AdminMessage::create($data);

        return redirect()->route('admin.message.index', compact('message'))->with('message', 'メッセージを返信しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminMessage $message)
    {
        $users = User::all();
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.edit', compact('message', 'users', 'currentPage'));
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
            'text'     => $request->text,
        ];

        if ($action == '送信') {
            $data['action'] = 1;
            $message->update($data);
            return redirect()->route('admin.message.index')->with('message', 'メッセージを送信しました');
        } else {
            $data['action'] = 0;
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
        return Redirect::back()->with('danger', $message->title . 'を削除しました');
    }

    /**
     * 非表示
     */
    public function hidden(UserMessage $message)
    {
        $hidden = UserMessage::find($message->id);

        if ($message->hidden == 1) {
            $hidden->update(['is_hidden' => 0]);
            return redirect()->route('admin.message.index')->with('success', $message->title . 'を復元しました');
        } else {
            $hidden->update(['is_hidden' => 1]);
            return redirect()->route('admin.message.index')->with('danger', $message->title . 'を削除しました');
        }
    }
}
