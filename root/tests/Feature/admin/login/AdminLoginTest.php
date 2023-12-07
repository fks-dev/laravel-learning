<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\AdminLogin;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Admin::create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    private function login()
    {
        $response = $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
        return $response;
    }

    private function logout()
    {
        $response = $this->delete('/admin/login');
        return $response;
    }

    /**
     * @test
     */
    public function ログイン画面にアクセスできる()
    {
        // ログインページにアクセス
        $response = $this->get('/admin/login');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function ログイン成功後に管理者管理画面にリダイレクトする()
    {
        $response = $this->login();

        // リダイレクトの確認
        $response->assertRedirect('/admin/admin-management');
    }

    /**
     * @test
     */
    public function ログイン成功時に認証されたユーザーが期待するユーザー名を持っている()
    {
        $this->login();

        // 認証されたユーザーが期待するユーザー名を持っているか確認
        $loginUser = Auth::guard('admin')->user();
        $this->assertSame('testAdmin', $loginUser->username);
    }

    /**
     * @test
     */
    public function ログイン成功時にセッションが再生成される()
    {
        // セッションIDの保持
        $sessionIDBeforeLogin = session()->getId();

        $this->login();

        // セッションIDの変化を確認
        $sessionIDAfterLogin = session()->getId();
        $this->assertNotSame($sessionIDBeforeLogin, $sessionIDAfterLogin);
    }

    /**
     * @test
     */
    public function ログイン成功時にログインログが記録される()
    {
        $this->login();

        // ログが記録されているか確認
        $loginUser = Auth::guard('admin')->user();
        $loginLog = AdminLogin::where('admin_id', $loginUser->id)->first();
        $this->assertNotNull($loginLog);
        $this->assertSame($loginUser->id, $loginLog->admin_id);
    }

    /**
     * @test
     */
    public function ログアウト処理が正常に行われる()
    {
        $this->login();

        $response = $this->logout();

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect(route('admin.login.index'));
        //ユーザーがゲスト状態（ログアウト状態）であるか確認
        $this->assertGuest('admin');
    }

    /**
     * @test
     */
    public function ログアウト後にセッションデータが破棄される()
    {
        $this->login();

        //セッションに値を設定
        $this->withSession(['key' => 'value']);

        $response = $this->logout();

        // セッションデータが破棄されていることを確認
        $response->assertSessionMissing('key');
    }

    /**
     * @test
     */
    public function ログアウト後にセッショントークンが再生成される()
    {
        $this->login();

        // セッショントークンの取得
        $tokenBeforeLogout = session('_token');

        $this->logout();

        // セッショントークンが再生成されていることを確認
        $tokenAfterLogout = session('_token');
        $this->assertNotSame($tokenBeforeLogout, $tokenAfterLogout);
    }
}
