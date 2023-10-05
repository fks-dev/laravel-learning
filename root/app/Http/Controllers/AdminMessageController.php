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
use App\Models\User;

class AdminMessageController extends Controller
{
    private const DEFAULT_PAGE_NUMBER = 1;

    /**
     * ログインユーザーのIDを取得
     */
    private function getAdminId()
    {
        return Auth::guard('admin')->user()->id;
    }

    /**
     * ユーザー情報取得
     */
    private function getUserAll()
    {
        return User::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $adminId = $this->getAdminId();
        $messages = UserMessage::withTrashed()
                                ->where('admin_id', $adminId)
                                ->where('action', '=', 1)
                                ->where('is_hidden', '=', 0)
                                ->orderByDesc('id')
                                ->paginate(10);
        $users = $this->getUserAll();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.index', compact('messages', 'users'));
    }

    /**
     * 下書き一覧
     */
    public function draft(Request $request)
    {
        $adminId = $this->getAdminId();
        $messages = AdminMessage::where('admin_id', $adminId)
                                ->where('action', '!=', 1)
                                ->orderByDesc('updated_at')
                                ->paginate(10);
        $users = $this->getUserAll();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.draftIndex', compact('messages', 'users'));
    }

    /**
     * 送信済み一覧
     */
    public function sent(Request $request)
    {
        $adminId = $this->getAdminId();
        $messages = AdminMessage::where('admin_id', $adminId)
                                ->where('action', '=', 1)
                                ->orderByDesc('updated_at')
                                ->paginate(10);
        $users = $this->getUserAll();
        Session::put('pageNumber', $request->get('page', 1));
        return view('admin.messages.sentIndex', compact('messages', 'users'));
    }

    /**
     * ゴミ箱
     */
    public function dust(Request $request)
    {
        $adminId = $this->getAdminId();
        $adminMessages = AdminMessage::onlyTrashed()->where('admin_id', $adminId)->get();
        $messages = $adminMessages->map(function ($item) {
            $item->is_hidden = 0;
            return $item;
        });
        $userMessages  = UserMessage::where('is_hidden', 1)->where('admin_id', $adminId)->get();
        $combinedMessages = $messages->concat($userMessages)->sortByDesc('updated_at');
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
            ['path' => route('admin.message.dust')]
        );

        return view('admin.messages.dust', compact('paginator', 'action'));
    }

    // 復元
    public function restore($message)
    {
        $record = AdminMessage::withTrashed()->find($message);
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
            $backRoute = route('admin.message.draft');
        } elseif ($source === 'send') {
            $backRoute = route('admin.message.sent');
        } elseif ($source === 'dust') {
            $backRoute = route('admin.message.dust');
        } else {
            $backRoute = route('admin.message.index');
        }
        $users = $this->getUserAll();
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.create', compact('users', 'currentPage', 'backRoute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $adminId = $this->getAdminId();

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
            return redirect()->route('admin.message.draft')->with('message', '下書きを保存しました');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMessage $message, Request $request)
    {
        $source = $request->input('source');
        if ($source === 'dust') {
            $source = true;
            $backRoute = route('admin.message.dust');
        } else {
            $source = false;
            $backRoute = route('admin.message.index');
        }
        $users = $this->getUserAll();
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.show', compact('message', 'source', 'users', 'currentPage', 'backRoute'));
    }

    /**
     * 送信済みShow
     */
    public function sentShow(AdminMessage $message, Request $request)
    {
        $source = true;
        $backRoute = route('admin.message.sent');
        $users = $this->getUserAll();
        $currentPage = Session::get('pageNumber', 1);
        return view('admin.messages.show', compact('message', 'source', 'users', 'currentPage', 'backRoute'));

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
        $adminId = $this->getAdminId();

        $data = [
            'admin_id' => $adminId,
            'user_id'  => $request->user_id,
            'title'    => $request->title,
            'text'     => $request->text,
            'reply_message_id' => $message,
        ];

        if ($action == '下書き') {
            $data['action'] = 2;
            AdminMessage::create($data);
            return redirect()->route('admin.message.index', compact('message'))->with('message', '下書きを保存しました');

        } elseif ($action == '送信') {
            $data['action'] = 1;
            AdminMessage::create($data);
            // 返信フラッグ
            $userMessage = UserMessage::find($message);
            $userMessage->is_replied = true;
            $userMessage->save();
            return redirect()->route('admin.message.index', compact('message'))->with('message', 'メッセージを返信しました');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminMessage $message)
    {
        $users = $this->getUserAll();
        $currentPage = Session::get('pageNumber', 1);

        if ($message->action = 2) {
            $reply = UserMessage::find($message->reply_message_id);
        } else {
            $reply = null;
        }

        return view('admin.messages.edit', compact('message', 'users', 'reply', 'currentPage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, AdminMessage $message)
    {
        $adminId = $this->getAdminId();

        $data = [
            'admin_id' => $adminId,
            'user_id'  => $request->user_id,
            'title'    => $request->title,
            'text'     => $request->text,
        ];

        if ($action == '送信') {
            if ($message->action == $type[2]) {
                // 返信フラッグ
                $userMessage = UserMessage::find($message->reply_message_id);
                $userMessage->is_replied = true;
                $userMessage->save();
            }
            $data['action'] = 1;
            $message->update($data);
            return redirect()->route('admin.message.index')->with('message', 'メッセージを送信しました');
        } else {
            if ($message->action == $type[2]) {
                $data['action'] = 2;
            } else {
                $data['action'] = 0;
            }
            $message->update($data);
            return redirect()->route('admin.message.draft')->with('message', '下書きを保存しました');
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

        if ($message->is_hidden == true) {
            $hidden->update(['is_hidden' => false]);
            return redirect()->route('admin.message.dust')->with('success', $message->title . 'を復元しました');
        } else {
            $hidden->update(['is_hidden' => true]);
            return redirect()->route('admin.message.index')->with('danger', $message->title . 'を削除しました');
        }
    }
}
