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
     * 管理者情報取得
     */
    private function getAdminAll()
    {
        return Admin::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();
        $messages = AdminMessage::withTrashed()
                                ->where('user_id', $userId)
                                ->where('action', '=', ActionEnum::SEND)
                                ->where('is_hidden', '=', false)
                                ->orderByDesc('id')
                                ->paginate(config('constants.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        return view('users.messages.index', compact('messages', 'admins'));
    }

    /**
     * 下書き一覧
     */
    public function draft(Request $request)
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
                                ->where('action', '!=', ActionEnum::SEND)
                                ->orderByDesc('updated_at')
                                ->paginate(config('constants.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        return view('users.messages.draftIndex', compact('messages', 'admins'));
    }

    /**
     * 送信済み一覧
     */
    public function sent(Request $request)
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
                                ->where('action', '=', ActionEnum::SEND)
                                ->orderByDesc('updated_at')
                                ->paginate(config('constants.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
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
            $item->is_hidden = false;
            return $item;
        });
        $adminMessages  = AdminMessage::where('is_hidden', true)->where('user_id', $userId)->get();
        $combinedMessages = $messages->concat($adminMessages)->sortByDesc('updated_at');
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        $action = ActionEnum::cases();

        // カスタムページネーション
        $ItemsPerPage = config('constants.ITEMS_PER_PAGE');
        $page = $request->get('page', self::DEFAULT_PAGE_NUMBER);
        $paginator = new LengthAwarePaginator(
            $combinedMessages->forPage($page, $ItemsPerPage),
            $combinedMessages->count(),
            $ItemsPerPage,
            $page,
            ['path' => route('users.messages.dust')]
        );

        return view('users.messages.dust', compact('paginator', 'action'));
    }

    // 復元
    public function restore($message)
    {
        $record = UserMessage::withTrashed()->find($message);
        $record->restore();
        return redirect()->back()->with('success', $record->title . 'を復元しました。');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $source = $request->input('source');
        if ($source === 'draft') {
            $backRoute = route('users.messages.draft');
        } elseif ($source === 'send') {
            $backRoute = route('users.messages.sent');
        } elseif ($source === 'dust') {
            $backRoute = route('users.messages.dust');
        } else {
            $backRoute = route('users.messages.index');
        }
        $admins = $this->getAdminAll();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
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

        if ($request->has(ActionEnum::SEND->value)) {
            $data['action'] = ActionEnum::SEND;
            UserMessage::create($data);
            return redirect()->route('users.messages.index')->with('message', 'メッセージを送信しました');
        } else {
            $data['action'] = ActionEnum::DRAFT;
            UserMessage::create($data);
            return redirect()->route('users.messages.draft')->with('message', '下書きを保存しました');
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
            $backRoute = route('users.messages.dust');
        } else {
            $source = false;
            $backRoute = route('users.messages.index');
        }
        $admins = $this->getAdminAll();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
        return view('users.messages.show', compact('message', 'admins', 'source', 'currentPage', 'backRoute'));
    }

    public function sentShow(UserMessage $message, Request $request)
    {
        $source = true;
        $backRoute = route('users.messages.sent');
        $admins = $this->getAdminAll();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
        return view('users.messages.show', compact('message', 'source', 'admins', 'currentPage', 'backRoute'));
    }

    /**
     * 返信画面
     */
    public function reply(AdminMessage $message)
    {
        $admin = $message->admin;
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

        if ($request->has(ActionEnum::DRAFT->value)) {
            $data['action'] = ActionEnum::NO_REPLY;
            UserMessage::create($data);
            return redirect()->route('users.messages.index', compact('message'))->with('message', '下書きを保存しました');
        } else {
            $data['action'] = ActionEnum::SEND;
            UserMessage::create($data);
            // 返信フラッグ
            $adminMessage = AdminMessage::find($message);
            $adminMessage->is_replied = true;
            $adminMessage->save();
            return redirect()->route('users.messages.index', compact('message'))->with('message', 'メッセージを返信しました');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMessage $message)
    {
        $admins = $this->getAdminAll();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);

        if ($message->action === ActionEnum::NO_REPLY) {
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

        if ($request->has(ActionEnum::DRAFT->value)) {
            $data['action'] = $message->action === ActionEnum::NO_REPLY ? ActionEnum::NO_REPLY : ActionEnum::DRAFT;
            $message->update($data);
            return redirect()->route('users.messages.draft')->with('message', '下書きを保存しました');
        }

        if ($message->action === ActionEnum::NO_REPLY) {
            // 返信フラッグ
            $adminMessage = AdminMessage::find($message->reply_message_id);
            $adminMessage->is_replied = true;
            $adminMessage->save();
        }
        $data['action'] = ActionEnum::SEND;
        $message->update($data);
        return redirect()->route('users.messages.index')->with('message', 'メッセージを送信しました');
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

        if ($message->is_hidden) {
            $hidden->update(['is_hidden' => false]);
            return redirect()->route('users.messages.dust')->with('success', $message->title . 'を復元しました');
        } else {
            $hidden->update(['is_hidden' => true]);
            return redirect()->route('users.messages.index')->with('danger', $message->title . 'を削除しました');
        }
    }
}
