<?php

namespace App\Http\Controllers;

use App\Enums\ActionEnum;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Models\Admin;

class UserMessageController extends Controller
{
    private const DEFAULT_PAGE_NUMBER = 1;

    /**
     * ログインユーザーのIDを取得
     */
    private function getUserId()
    {
        return Auth::guard('web')->user()->id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();
        $messages = AdminMessage::withTrashed()
                                ->where('user_id', $userId)
                                ->where('action', '=', 1)
                                ->where('is_hidden', '=', 0)
                                ->orderByDesc('id')
                                ->paginate(10);
        $admins = Admin::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('users.messages.index', compact('messages', 'admins'));
    }

    /**
     * 下書き一覧
     */
    public function draft(Request $request)
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
                                ->where('action', '!=', 1)
                                ->orderByDesc('updated_at')
                                ->paginate(10);
        $admins = Admin::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('users.messages.draftIndex', compact('messages', 'admins'));
    }

    /**
     * 送信済み一覧
     */
    public function sent(Request $request)
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
                                ->where('action', '=', 1)
                                ->orderByDesc('updated_at')
                                ->paginate(10);
        $admins = Admin::all();
        Session::put('pageNumber', $request->get('page', 1));
        return view('users.messages.sentIndex', compact('messages', 'admins'));
    }

    /**
     * ゴミ箱
     */
    public function dust(Request $request)
    {
        $userId = $this->getUserId();
        $userMessages = UserMessage::onlyTrashed()->where('user_id', $userId)->get();
        $messages = $userMessages->map(function ($item) {
            $item->is_hidden = 0;
            return $item;
        });
        $adminMessages  = AdminMessage::where('is_hidden', 1)->where('user_id', $userId)->get();
        $combinedMessages = $messages->concat($adminMessages)->sortByDesc('updated_at');
        Session::put('pageNumber', $request->get('page', 1));
        $action = ActionEnum::cases();

        // カスタムページネーション
        $perPage = 10;
        $page = request('page', 1);
        Session::put('pageNumber', $page);
        $paginator = new LengthAwarePaginator(
            $combinedMessages->forPage($page, $perPage),
            $combinedMessages->count(),
            $perPage,
            $page,
            ['path' => route('users.message.dust')]
        );

        return view('users.messages.dust', compact('paginator', 'action'));
    }

    // 復元
    public function restore($message)
    {
        $record = UserMessage::withTrashed()->find($message);
        $record->restore();
        return redirect()->back()->with('success', $record->title.'を復元しました。');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $source = $request->input('source');
        if ($source === 'draft') {
            $backRoute = route('users.message.draft');
        } elseif ($source === 'send') {
            $backRoute = route('users.message.sent');
        } elseif ($source === 'dust') {
            $backRoute = route('users.message.dust');
        } else {
            $backRoute = route('users.message.index');
        }
        $admins = Admin::all();
        $currentPage = Session::get('pageNumber', 1);
        return view('users.messages.create', compact('admins', 'currentPage', 'backRoute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $userId = $this->getUserId();

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
        ];

        if ($action == '送信') {
            $data['action'] = 1;
            UserMessage::create($data);
            return redirect()->route('users.message.index')->with('message', 'メッセージを送信しました');
        } else {
            $data['action'] = 0;
            UserMessage::create($data);
            return redirect()->route('users.message.draft')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminMessage $message, Request $request)
    {
        $source = $request->input('source');
        if ($source === 'dust') {
            $source = true;
            $backRoute = route('users.message.dust');
        } else {
            $source = false;
            $backRoute = route('users.message.index');
        }
        $admins = Admin::all();
        $currentPage = Session::get('pageNumber', 1);
        return view('users.messages.show', compact('message', 'admins', 'source', 'currentPage', 'backRoute'));
    }

    public function sentShow(UserMessage $message, Request $request)
    {
        $source = true;
        $backRoute = route('users.message.sent');
        $admins = Admin::all();
        $currentPage = Session::get('pageNumber', 1);
        return view('users.messages.show', compact('message', 'source', 'admins', 'currentPage', 'backRoute'));

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
        $userId = $this->getUserId();

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
            'reply_message_id' => $message,
        ];

        if ($action == '下書き') {
            $data['action'] = 2;
            UserMessage::create($data);
            return redirect()->route('users.message.index', compact('message'))->with('message', '下書きを保存しました');

        } elseif ($action == '送信') {
            $data['action'] = 1;
            UserMessage::create($data);
            // 返信フラッグ
            $adminMessage = AdminMessage::find($message);
            $adminMessage->is_replied = true;
            $adminMessage->save();
            return redirect()->route('users.message.index', compact('message'))->with('message', 'メッセージを返信しました');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMessage $message)
    {
        $admins = Admin::all();
        $currentPage = Session::get('pageNumber', 1);

        if ($message->action = 2) {
            $reply = AdminMessage::find($message->reply_message_id);
        } else {
            $reply = null;
        }

        return view('users.messages.edit', compact('message', 'admins', 'reply', 'currentPage'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, UserMessage $message)
    {
        $userId = $this->getUserId();

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
        ];

        if ($action == '送信') {
            if ($message->action == $type[2]) {
                // 返信フラッグ
                $adminMessage = AdminMessage::find($message->reply_message_id);
                $adminMessage->is_replied = true;
                $adminMessage->save();
            }
            $data['action'] = 1;
            $message->update($data);
            return redirect()->route('users.message.index')->with('message', 'メッセージを送信しました');
        } else {
            if ($message->action == $type[2]) {
                $data['action'] = 2;
            } else {
                $data['action'] = 0;
            }
            $message->update($data);
            return redirect()->route('users.message.draft')->with('message', '下書きを保存しました');
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
        $hidden = AdminMessage::find($message->id);

        if ($message->is_hidden == true) {
            $hidden->update(['is_hidden' => false]);
            return redirect()->route('users.message.dust')->with('success', $message->title . 'を復元しました');
        } else {
            $hidden->update(['is_hidden' => true]);
            return redirect()->route('users.message.index')->with('danger', $message->title . 'を削除しました');
        }
    }
}
