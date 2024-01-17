<?php

namespace Tests\Feature\Admin\AdminMessages;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Enums\ActionEnum;

class AdminMessagesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $user;
    private $adminMessage;
    private $userMessage;

    /**
     * テストデータを作成
     */
    public function setUp(): void
    {
        parent::setUp();

        //管理者ユーザーを作成
        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        //管理者としてログインする
        $this->actingAs($this->admin, 'admin');

        //ユーザーを作成
        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('password1'),
            'mail_address' => 'testUser1@user.com',
        ]);

        //デフォルトの管理者メッセージ
        $this->adminMessage = AdminMessage::factory()->create([
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::DRAFT,
            'is_hidden' => false,
            'is_replied' => false,
            'reply_message_id' => null,
        ]);

        //デフォルトの受信メッセージ
        $this->userMessage = UserMessage::factory()->create([
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_user_message',
            'text' => 'This is test_user_message.',
            'action' => ActionEnum::SEND,
            'is_hidden' => false,
            'is_replied' => false,
            'reply_message_id' => null,
        ]);
    }

    /**
     * 更新日時の異なる管理者メッセージを100件作成するメソッド
     */
    private function prepareTestAdminMessages($action)
    {
        for ($i = 1; $i <= 100; $i++) {
            AdminMessage::factory()->create([
                'id' => 16000 + $i,
                'admin_id' => $this->admin->id,
                'user_id' => $this->user->id,
                'title' => 'test_admin_message_' . $i,
                'text' => 'This is test_admin_message.',
                'action' => $action,
                'updated_at' => now()->subDays($i),
            ]);
        }
    }

    /**
     * 受信メッセージを100件作成するメソッド
     */
    private function prepareTestUserMessages()
    {
        for ($i = 1; $i <= 100; $i++) {
            UserMessage::factory()->create([
                'id' => 15000 + $i,
                'admin_id' => $this->admin->id,
                'user_id' => $this->user->id,
                'title' => 'test_user_message_' . $i,
                'text' => 'This is test_admin_message.',
                'action' => ActionEnum::SEND,
                'is_hidden' => false,
            ]);
        }
    }

    /**受信一覧画面**/

    /**
     * @test
     * 管理者が受信一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_get_ok()
    {
        $response = $this->get('/admin/messages');
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に受信一覧画面にアクセスできないことを確認する
     */
    public function test_admin_messages_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/admin/messages');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 受信一覧画面でメッセージが1ページに50件ずつ、idの降順に並んでいることを確認する
     */
    public function test_admin_messages_get_ok_sort()
    {
        //テストデータを準備
        $this->prepareTestUserMessages();

        //ビューにアクセス
        $response = $this->get('/admin/messages');

        //ページネーションが正しく表示されていることを確認
        $response->assertSee('pagination');

        //ビューに必要なデータが渡されていることを確認
        $response->assertViewHasAll(['messages', 'users', 'adminUser']);

        //ビューに渡されたメッセージの期待される表示順序
        $expectedOrder = collect($response->original->getData()['messages'])->sortByDesc('id')->pluck('id')->toArray();

        //メッセージ一覧を取得し、取得された表示順序が期待される表示順序と一致するか確認
        $sortedOrder = collect($response->original->getData()['messages'])->pluck('id')->toArray();
        $this->assertEquals($expectedOrder, $sortedOrder);
    }

    /**
     * @test
     * 受信一覧画面でセッションに正しいページ番号が保存されていることを確認する
     */
    public function test_admin_messages_get_ok_session()
    {
        //ビューにアクセスし、ビューに渡されたページ番号を取得
        $response = $this->get('/admin/messages');
        $viewPageNumber = $response->original->getData()['messages']->currentPage();

        //セッションに保存されたページ番号を取得
        $sessionPageNumber = session('pageNumber');

        //ビューに渡されたページ番号とセッションに保存された実際のページ番号が一致するか確認
        $this->assertEquals($sessionPageNumber, $viewPageNumber);
    }

    /**受信メッセージ詳細画面**/

    /**
     * 受信メッセージの詳細画面へ遷移してきた場合、ビューに含まれているバックルートの値が、
     * 期待されるバックルートと一致するか確認するアサーションメソッド
     */
    private function assertBackRouteFromShow($source, $expectedBackRoute)
    {
        // 受信メッセージの詳細画面にアクセスし、ビューからバックルートの値を取得
        $response = $this->get("/admin/messages/{$this->userMessage->id}?source={$source}");
        $actualBackRoute =  $response->viewData('backRoute');

        // ビューから取得した値が期待される値と一致するか確認
        $this->assertEquals($expectedBackRoute, $actualBackRoute);
    }

    /**
     * @test
     * 管理者が受信メッセージの詳細画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_get_ok_show()
    {
        $response = $this->get("/admin/messages/{$this->userMessage->id}");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に受信メッセージの詳細画面にアクセスできないことを確認する
     */
    public function test_admin_messages_get_ok_show_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get("/admin/messages/{$this->userMessage->id}");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * ゴミ箱画面から受信メッセージの詳細画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元のゴミ箱画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_get_ok_show_backroute_from_dust()
    {
        // ゴミ箱画面から遷移してきた場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages/dust';

        $this->assertBackRouteFromShow('dust', $expectedBackRoute);
    }

    /**
     * @test
     * 受信一覧画面から受信メッセージの詳細画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元の受信一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_get_ok_show_backroute_from_index()
    {
        //受信一覧画面から遷移してきら場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages';

        $this->assertBackRouteFromShow('index', $expectedBackRoute);
    }

    /**受信メッセージの削除（非表示）と復元（表示）**/

    /**
     * 管理者が受信メッセージを削除（非表示に）するメソッド
     */
    public function hiddenMessagesTrue()
    {
        return $this->post("/admin/messages/{$this->userMessage->id}/hidden", [
            'is_hidden' => true,
        ]);
    }

    /**
     * 管理者が削除した受信メッセージを復元（表示）するメソッド
     */
    public function hiddenMessagesFalse()
    {
        //テストデータの準備
        $this->userMessage->update([
            'is_hidden' => true,
        ]);

        return $this->post("/admin/messages/{$this->userMessage->id}/hidden", [
            'is_hidden' => false,
        ]);
    }

    /* @test
    * 管理者が受信メッセージを削除できることを確認する
    */
    public function test_admin_messages_hidden_post_ok_true()
    {
        $this->hiddenMessagesTrue();

        $this->assertDatabaseHas('user_messages', [
            'id' => $this->userMessage->id,
            'is_hidden' => true,
        ]);
    }

    /**
     * @test
     * 管理者が受信メッセージの削除を完了すると、受信一覧画面へリダイレクトし、
     * リダイレクト先で「メッセージを削除しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_hidden_post_ok_redirect_true()
    {
        $response = $this->hiddenMessagesTrue();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages');

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('danger', $this->userMessage->title . 'を削除しました');
    }

    /**
     * @test
     * 管理者が削除した受信メッセージを復元できることを確認する
     */
    public function test_admin_messages_hidden_post_ok_false()
    {
        $this->hiddenMessagesFalse();

        // データベース上で受信メッセージが復元されたか確認
        $this->assertDatabaseHas('user_messages', [
            'id' => $this->userMessage->id,
            'is_hidden' => false,
        ]);
    }

    /**
     * @test
     * 管理者が削除した受信メッセージの復元を完了すると、ゴミ箱画面へリダイレクトし、
     * リダイレクト先で「メッセージを復元しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_hidden_post_ok_redirect_false()
    {
        $response = $this->hiddenMessagesFalse();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages/dust');

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('success', $this->userMessage->title . 'を復元しました');
    }

    /**返信画面**/

    /**
     * @test
     * 管理者が返信画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_reply_get_ok()
    {
        $response = $this->get("/admin/messages/{$this->userMessage->id}/reply");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に返信画面にアクセスできないことを確認する
     */
    public function test_admin_messages_reply_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get("/admin/messages/{$this->userMessage->id}/reply");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**返信メッセージの下書き保存と送信**/

    /**
     * 管理者が返信メッセージを作成して保存するメソッド
     */
    public function replyStoreMessageDraft()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'reply_message_id' => $this->userMessage->id,
        ]);

        return $this->post("/admin/messages/{$this->adminMessage->reply_message_id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::DRAFT->value => '下書き',
        ]);
    }

    /**
     * 管理者が返信メッセージを作成して送信するメソッド
     */
    public function replyStoreMessageSend()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'reply_message_id' => $this->userMessage->id,
        ]);

        return $this->post("/admin/messages/{$this->adminMessage->reply_message_id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::SEND->value => '送信',
        ]);
    }

    /**
     * @test
     * 管理者が返信メッセージを作成して保存できることを確認する
     */
    public function test_admin_messages_post_ok_replyStore_draft()
    {
        $this->replyStoreMessageDraft();

        // データベース上で新しいメッセージが追加されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::NO_REPLY,
            'reply_message_id' => $this->userMessage->id,
        ]);
    }

    /**
     * @test
     * 管理者が返信メッセージを作成して保存を完了すると、受信一覧画面へリダイレクトし、
     * リダイレクト先で「下書きを保存しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_post_ok_replyStore_redirect_draft()
    {
        $response = $this->replyStoreMessageDraft();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/messages?message={$this->adminMessage->reply_message_id}");

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', '下書きを保存しました');
    }

    /**
     * @test
     * 管理者が返信メッセージを作成して送信できることを確認する
     */
    public function test_admin_messages_post_ok_replyStore_send()
    {
        $this->replyStoreMessageSend();

        // データベース上で新しいメッセージが追加されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::SEND->value,
            'reply_message_id' => $this->userMessage->id,
        ]);
    }

    /**
     * @test
     * 管理者が返信メッセージを作成して送信を完了すると、受信一覧覧画面へリダイレクトし、
     * リダイレクト先で「メッセージを返信しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_post_ok_replyStore_redirect_send()
    {
        $response = $this->replyStoreMessageSend();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/messages?message={$this->adminMessage->reply_message_id}");

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'メッセージを返信しました');
    }

    /* @test
    * 管理者が返信メッセージを作成して送信を完了すると、
    * 返信元の受信メッセージに返信フラッグがつくことを確認する
    */
    public function test_admin_messages_post_ok_replyStore_flag()
    {
        $this->replyStoreMessageSend();

        //データベース上で is_replied カラムが true に更新されたか確認
        $this->assertTrue($this->userMessage->fresh()->is_replied);
    }

    /**新規作成画面**/

    /**
     * 新規作成画面へ遷移してきた場合、ビューに含まれているバックルートの値が、
     * 期待されるバックルートと一致するか確認するアサーションメソッド
     */
    private function assertBackRouteFromCreate($source, $expectedBackRoute)
    {
        // メッセージの新規作成画面にアクセスし、ビューからバックルートの値を取得
        $response = $this->get("/admin/messages/create?source={$source}");
        $actualBackRoute =  $response->viewData('backRoute');

        // ビューから取得した値が期待される値と一致するか確認
        $this->assertEquals($expectedBackRoute, $actualBackRoute);
    }

    /**
     * @test
     * 管理者がメッセージの新規作成画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_create_get_ok()
    {
        $response = $this->get('/admin/messages/create');
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にメッセージの新規作成画面にアクセスできないことを確認する
     */
    public function test_admin_messages_create_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/admin/messages/create');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 下書き一覧画面から新規登録画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元の下書き一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_create_get_ok_backroute_from_draft()
    {
        //下書き一覧画面から遷移してきた場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages/draft';

        $this->assertBackRouteFromCreate('draft', $expectedBackRoute);
    }

    /**
     * @test
     * 送信済み一覧画面から新規登録画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元の送信済み一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_create_get_ok_backroute_from_sent()
    {
        //送信済み一覧覧画面から遷移してきら場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages/sent';

        $this->assertBackRouteFromCreate('send', $expectedBackRoute);
    }

    /**
     * @test
     * ゴミ箱画面から新規登録画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元のゴミ箱画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_create_get_ok_backroute_from_dust()
    {
        //ゴミ箱画面から遷移してきら場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages/dust';

        $this->assertBackRouteFromCreate('dust', $expectedBackRoute);
    }

    /**
     * @test
     * 受信一覧画面から新規登録画面へ遷移してきた場合、<<戻るリンクをクリックすると、
     * 遷移元の受信一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_create_get_ok_backroute_from_index()
    {
        //受信一覧画面から遷移してきら場合に期待されるバックルート
        $expectedBackRoute = config('app.url') . '/admin/messages';

        $this->assertBackRouteFromCreate('index', $expectedBackRoute);
    }

    /**新規メッセージの下書き保存と送信**/

    /**
     * 管理者が新規メッセージを作成して保存するメソッド
     */
    public function storeMessageDraft()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::DRAFT,
        ]);

        return $this->post("/admin/messages", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::DRAFT->value => '下書き',
        ]);
    }

    /**
     * 管理者が新規メッセージを作成して送信するメソッド
     */
    public function storeMessageSend()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::SEND,
        ]);

        return $this->post("/admin/messages", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::SEND->value => '送信',
        ]);
    }

    /**
     * @test
     * 管理者が新規メッセージを保存できることを確認する
     */
    public function test_admin_messages_create_post_ok_draft()
    {
        $this->storeMessageDraft();

        // データベース上で新しいメッセージが追加されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::DRAFT,
        ]);
    }

    /**
     * @test
     * 管理者が新規メッセージの保存を完了すると、下書き一覧画面へリダイレクトし、
     * リダイレクト先で「下書きを保存しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_create_post_ok_redirect_to_draft()
    {
        $response = $this->storeMessageDraft();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages/draft');

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', '下書きを保存しました');
    }

    /**
     * @test
     * 管理者が新規メッセージを作成して送信できることを確認する
     */
    public function test_admin_messages_create_post_ok_send()
    {
        $this->storeMessageSend();

        // データベース上で新しいメッセージが追加されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::SEND,
        ]);
    }

    /**
     * @test
     * 管理者が新規メッセージを作成して送信を完了すると、受信一覧画面へリダイレクトし、
     * リダイレクト先で「メッセージを送信しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_create_post_ok_redirect_to_send()
    {
        $response = $this->storeMessageSend();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages');

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'メッセージを送信しました');
    }

    /**下書き一覧画面**/

    /**
     * @test
     * 管理者が下書き一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_draft_get_ok()
    {
        $response = $this->get('/admin/messages/draft');
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に下書き一覧画面にアクセスできないことを確認する
     */
    public function test_admin_messages_draft_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/admin/messages/draft');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 下書き一覧画面でメッセージが1ページに50件ずつ、更新日時の降順に並んでいることを確認する
     */
    public function test_admin_messages_draft_get_ok_sort()
    {
        //テストデータの準備
        $this->prepareTestAdminMessages(ActionEnum::DRAFT);

        //ビューにアクセス
        $response = $this->get('/admin/messages/draft');

        //ビューに必要なデータが渡されていることを確認
        $response->assertViewHasAll(['messages', 'users', 'adminUser']);

        //ビューに渡されたメッセージの期待される表示順序
        $expectedOrder = collect($response->original->getData()['messages'])->sortByDesc('updated_at')->pluck('id')->toArray();

        //メッセージ一覧を取得し、取得された表示順序が期待される表示順序と一致するか確認
        $sortedOrder = collect($response->original->getData()['messages'])->pluck('id')->toArray();
        $this->assertEquals($expectedOrder, $sortedOrder);

        //ページネーションが正しく表示されていることを確認
        $response->assertSeeTextInOrder(range(1, config('constants.ITEMS_PER_PAGE')));
    }

    /**
     * @test
     * 下書き一覧画面でセッションに正しいページ番号が保存されていることを確認する
     */
    public function test_admin_messages_draft_get_ok_session()
    {
        //ビューにアクセスし、ビューに渡されたページ番号を取得
        $response = $this->get('/admin/messages/draft');
        $viewPageNumber = $response->original->getData()['messages']->currentPage();

        //セッションに保存されたページ番号を取得
        $sessionPageNumber = session('pageNumber');

        //ビューに渡されたページ番号とセッションに保存された実際のページ番号が一致するか確認
        $this->assertEquals($sessionPageNumber, $viewPageNumber);
    }

    /**下書き編集画面**/

    /**
     * @test
     * 管理者がメッセージの編集画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_edit_get_ok()
    {
        $response = $this->get("/admin/messages/{$this->adminMessage->id}/edit");

        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にメッセージの編集画面にアクセスできないことを確認する
     */
    public function test_admin_messages_edit_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get("/admin/messages/{$this->adminMessage->id}/edit");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 編集するメッセージのアクションが NO_REPLY の場合、
     * 編集画面の「メッセージ内容」欄に返信元の受信メッセージの本文が表示されることを確認する
     */
    public function test_admin_messages_edit_get_ok_noreply()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::NO_REPLY,
            'reply_message_id' => $this->userMessage->id,
        ]);

        $response = $this->get("/admin/messages/{$this->adminMessage->id}/edit");

        //「メッセージ内容」欄に受信メッセージの本文が表示されているか確認
        $response->assertViewHas('reply', $this->userMessage);
    }

    /**
     * @test
     * 編集するメッセージのアクションが NO_REPLY でない場合、
     * 編集画面の「メッセージ内容」欄が表示されないことを確認する
     */
    public function test_admin_messages_edit_get_ok_draft()
    {
        //編集するメッセージのアクションが DRAFT の場合
        $response = $this->get("/admin/messages/{$this->adminMessage->id}/edit");

        //「メッセージ内容」欄が表示されていないか確認
        $response->assertViewHas('reply', null);
    }

    /**下書きの編集**/

    /**
     * 管理者がメッセージの下書きを更新して保存するメソッド
     */
    public function updateMessageDraft()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
        ]);

        return $this->patch("/admin/messages/{$this->adminMessage->id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::DRAFT->value => 0,
        ]);
    }

    /**
     * 管理者が未返信のメッセージの下書きを更新して保存するメソッド
     */
    public function updateMessageNoreply()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::NO_REPLY,
        ]);

        return $this->patch("/admin/messages/{$this->adminMessage->id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            ActionEnum::DRAFT->value => 0,
        ]);
    }

    /**
     * 管理者がメッセージの下書きを更新して送信するメソッド
     */
    public function updateMessageSend()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::SEND,
        ]);

        return $this->patch("/admin/messages/{$this->adminMessage->id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            'action' => ActionEnum::SEND->value,
        ]);
    }

    /**
     * @test
     * 管理者がメッセージの下書きを更新して保存できることを確認する
     * （現在のメッセージのアクションが ActionEnum::DRAFT の場合、
     * 更新して保存しても ActionEnum::DRAFT が設定されることを確認する）
     */
    public function test_admin_messages_patch_ok_update_draft()
    {
        $this->updateMessageDraft();

        //データベース上でメッセージが更新されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::DRAFT,
        ]);
    }

    /**
     * @test
     * メッセージの下書きを更新して保存が完了すると、下書き一覧画面へリダイレクトし、
     * リダイレクト先で「下書きを保存しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_patch_ok_redirect_update_draft()
    {
        $response = $this->updateMessageDraft();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages/draft');

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', '下書きを保存しました');
    }

    /**
     * @test
     * 管理者が未返信のメッセージの下書きを更新して保存できることを確認する
     * （現在のメッセージのアクションが ActionEnum::NO_REPLY の場合、
     * 更新して保存しても ActionEnum::NO_REPLY が設定されることを確認する）
     */
    public function test_admin_messages_patch_ok_update_noreply()
    {
        $this->updateMessageNoreply();

        //データベース上でメッセージが更新されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::NO_REPLY,
        ]);
    }

    /**
     * @test
     * 未返信のメッセージの下書きを更新して保存が完了すると、下書き一覧画面へリダイレクトし、
     * リダイレクト先で「下書きを保存しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_patch_ok_redirect_update_noreply()
    {
        $response = $this->updateMessageNoreply();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages/draft');

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', '下書きを保存しました');
    }

    /* @test
    * 管理者がメッセージの下書きを更新して送信できることを確認する
    */
    public function test_admin_messages_patch_ok_update_send()
    {
        $this->updateMessageSend();

        //データベース上でメッセージが更新されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::SEND,
        ]);
    }

    /**
     * @test
     * メッセージの下書きを更新して送信が完了すると、受信一覧画面へリダイレクトし、
     * リダイレクト先で「メッセージを送信しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_patch_ok_redirect_update_send()
    {
        $response = $this->updateMessageSend();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect('/admin/messages');

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'メッセージを送信しました');
    }

    /* @test
    * 管理者が未返信のメッセージの下書きを更新して送信すると、
    * 返信元の受信メッセージに返信フラッグがつくことを確認する
    */
    public function test_admin_messages_patch_ok_update_flag()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'title' => 'test_admin_message_update',
            'text' => 'This is test_admin_message_update.',
            'action' => ActionEnum::NO_REPLY,
            'reply_message_id' => $this->userMessage->id,
        ]);

        $this->patch("/admin/messages/{$this->adminMessage->id}", [
            'admin_id' => $this->adminMessage->admin_id,
            'user_id' => $this->adminMessage->user_id,
            'title' => $this->adminMessage->title,
            'text' => $this->adminMessage->text,
            'action' => ActionEnum::SEND->value,
        ]);

        //データベース上で is_replied カラムが true に更新されたか確認
        $this->assertTrue($this->userMessage->fresh()->is_replied);
    }

    /**メッセージの下書きの論理削除**/

    /**
     * 管理者がメッセージの下書きを論理削除するメソッド
     */
    public function  destroyMessagesDraft()
    {
        return $this->delete("/admin/messages/{$this->adminMessage->id}");
    }

    /**
     * @test
     * 管理者がメッセージの下書きを論理削除できることを確認する
     */
    public function test_admin_messages_delete_ok_draft()
    {
        $this->destroyMessagesDraft();

        //データベース上でメッセージが論理削除されたか確認
        $this->assertSoftDeleted('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::DRAFT,
        ]);

        //deleted_atカラムが適切に設定されているか確認
        $this->assertNotNull($this->adminMessage->fresh()->deleted_at);
    }

    /**
     * @test
     * 送信済みメッセージの削除が完了すると、元いた画面へリダイレクトし、
     * 「メッセージを削除しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_delete_ok_redirect_draft()
    {
        $response = $this->destroyMessagesDraft();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect();

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('danger', $this->adminMessage->title . 'を削除しました');
    }

    /**送信済み一覧**/

    /**
     * @test
     * 管理者が送信済み一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_sent_get_ok()
    {
        $response = $this->get('/admin/messages/sent');
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に送信済み一覧画面にアクセスできないことを確認する
     */
    public function test_admin_messages_sent_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/admin/messages/sent');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 送信済み一覧画面でメッセージが1ページに50件ずつ、更新日時の順に並んでいることを確認する
     */
    public function test_admin_messages_sent_get_sort()
    {
        // テストデータを準備
        $this->prepareTestAdminMessages(ActionEnum::SEND);

        // ビューにアクセス
        $response = $this->get('/admin/messages/sent');

        // ビューに必要なデータが渡されていることを確認
        $response->assertViewHasAll(['messages', 'users', 'adminUser']);

        //ビューに渡されたメッセージの期待される表示順序
        $expectedOrder = collect($response->original->getData()['messages'])->sortByDesc('updated_at')->pluck('id')->toArray();

        //メッセージ一覧を取得し、取得された表示順序が期待される表示順序と一致するか確認
        $sortedOrder = collect($response->original->getData()['messages'])->pluck('id')->toArray();
        $this->assertEquals($expectedOrder, $sortedOrder);

        // ページネーションが正しく表示されているか確認
        $response->assertSeeTextInOrder(range(1, config('constants.ITEMS_PER_PAGE')));
    }

    /**
     * @test
     * 送信済み一覧画面でセッションに正しいページ番号が保存されていることを確認する
     */
    public function test_admin_messages_sent_get_ok_session()
    {
        //ビューにアクセスし、ビューに渡されたページ番号を取得
        $response = $this->get('/admin/messages/sent');
        $viewPageNumber = $response->original->getData()['messages']->currentPage();

        //セッションに保存されたページ番号を取得
        $sessionPageNumber = session('pageNumber');

        //ビューに渡されたページ番号とセッションに保存された実際のページ番号が一致するか確認
        $this->assertEquals($sessionPageNumber, $viewPageNumber);
    }

    /**送信済みメッセージの詳細画面**/

    /**
     * @test
     * 管理者が送信済みメッセージの詳細画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_sent_get_ok_sentShow()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::SEND,
        ]);

        $response = $this->get("/admin/messages/{$this->adminMessage->id}/sent");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時に送信済みメッセージの詳細画面にアクセスできないことを確認する
     */
    public function test_admin_messages_sent_get_ok_sentShow_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get("/admin/messages/{$this->adminMessage->id}/sent");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**送信済みメッセージの論理削除**/

    /**
     * 管理者が送信済みメッセージを論理削除するメソッド
     */
    public function destroyMessagesSend()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::SEND,
        ]);

        return $this->delete("/admin/messages/{$this->adminMessage->id}");
    }

    /**
     * @test
     * 管理者が送信済みメッセージを論理削除できることを確認する
     */
    public function test_admin_messages_delete_ok_send()
    {
        $this->destroyMessagesSend();

        //データベース上でメッセージが論理削除されたか確認
        $this->assertSoftDeleted('admin_messages', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->user->id,
            'title' => 'test_admin_message',
            'text' => 'This is test_admin_message.',
            'action' => ActionEnum::SEND,
        ]);

        // deleted_at カラムが適切に設定されているか確認
        $this->assertNotNull($this->adminMessage->fresh()->deleted_at);
    }

    /**
     * @test
     * 送信済みメッセージの削除が完了すると、元いた画面へリダイレクトし、
     * 「メッセージを削除しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_delete_ok_redirect_send()
    {
        $response = $this->destroyMessagesSend();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect();

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('danger', $this->adminMessage->title . 'を削除しました');
    }

    /**ゴミ箱画面**/

    /**
     * @test
     * 管理者がゴミ箱画面に正常にアクセスできることを確認する
     */
    public function test_admin_messages_dust_get_ok()
    {
        $response = $this->get('/admin/messages/dust');
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にゴミ箱画面にアクセスできないことを確認する
     */
    public function test_admin_messages_dust_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/admin/messages/dust');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * ゴミ箱画面でメッセージが更新日時の降順に並んでいることを確認する
     */
    public function test_admin_messages_dust_get_ok_sort()
    {
        //ビューにアクセス
        $response = $this->get('/admin/messages/dust');

        //ページネーションが正しく機能しているか確認
        $response->assertViewHas('paginator');

        //ページネーションからメッセージを取得
        $paginator = $response->viewData('paginator');

        //メッセージの期待される表示順序
        $expectedOrder = collect($paginator->items())->sortByDesc('updated_at')->values()->all();

        //取得された表示順序が期待される表示順序と一致するか確認
        $this->assertEquals($expectedOrder, $paginator->items());
    }

    /**
     * @test
     * ゴミ箱画面でセッションに正しいページ番号が保存されていることを確認する
     */
    public function test_admin_messages_dust_get_ok_session()
    {
        //ビューにアクセスし、ページネーションのための LengthAwarePaginator のインスタンスを取得
        $response = $this->get('/admin/messages/dust');
        $paginator = $response->viewData('paginator');

        //インスタンスビューに渡されたページ番号を取得
        $viewPageNumber = $paginator->currentPage();

        //セッションに保存されたページ番号を取得
        $sessionPageNumber = session('pageNumber');

        //ビューに渡されたページ番号とセッションに保存された実際のページ番号が一致するか確認
        $this->assertEquals($sessionPageNumber, $viewPageNumber);
    }

    /**論理削除したメッセージ復元の復元**/

    /**
     * 管理者が論理削除したメッセージの下書きを復元するメソッド
     */
    public function restoreMessagesDraft()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'deleted_at' => '2024-01-12 15:20:05',
        ]);

        return $this->post("/admin/messages/dust/{$this->adminMessage->id}", [
            'deleted_at' => null,
        ]);
    }

    /**
     * 管理者が論理削除した送信済みメッセージのを復元するメソッド
     */
    public function restoreMessagesSend()
    {
        //テストデータの準備
        $this->adminMessage->update([
            'action' => ActionEnum::SEND,
            'deleted_at' => '2024-01-12 15:20:05',
        ]);

        return $this->post("/admin/messages/dust/{$this->adminMessage->id}", [
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     * 管理者が論理削除したメッセージの下書きを復元できることを確認する
     */
    public function test_admin_messages_dust_post_ok_draft()
    {
        $this->restoreMessagesDraft();

        // データベース上でメッセージの下書きが復元されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'id' => $this->adminMessage->id,
            'deleted_at' => null,
        ]);

        // deleted_at カラムが適切に設定されているか確認
        $this->assertNull($this->adminMessage->fresh()->deleted_at);
    }

    /**
     * @test
     * 管理者がメッセージの下書きの復元を完了すると、元いた画面へリダイレクトし、
     * 「メッセージを復元しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_dust_post_ok_redirect_draft()
    {
        $response = $this->restoreMessagesDraft();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect();

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('success', $this->adminMessage->title . 'を復元しました。');
    }

    /**
     * @test
     * 管理者が論理削除した送信済みメッセージを復元できることを確認する
     */
    public function test_admin_messages_dust_post_ok_send()
    {
        $this->restoreMessagesSend();

        // データベース上で送信済みメッセージが復元されたか確認
        $this->assertDatabaseHas('admin_messages', [
            'id' => $this->adminMessage->id,
            'deleted_at' => null,
        ]);

        // deleted_at カラムが適切に設定されているか確認
        $this->assertNull($this->adminMessage->fresh()->deleted_at);
    }

    /**
     * @test
     * 管理者が送信済みメッセージの復元を完了すると、元いた画面へリダイレクトし、
     * 「メッセージを復元しました」の表示が出力されることを確認する
     */
    public function test_admin_messages_dust_post_ok_redirect_send()
    {
        $response = $this->restoreMessagesSend();

        // 正しいリダイレクトが行われているか確認
        $response->assertRedirect();

        // 正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('success', $this->adminMessage->title . 'を復元しました。');
    }
}
