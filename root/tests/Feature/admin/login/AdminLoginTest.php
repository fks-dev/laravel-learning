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
        // ログイン処理
        $response = $this->login();

        // リダイレクトの確認
        $response->assertRedirect('/admin/admin-management');
    }

    /**
     * @test
     */
    public function ログイン成功時に認証されたユーザーが期待するユーザー名を持っている()
    {
        // ログイン処理
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

        // ログイン処理
        $this->login();

        // セッションIDの変化を確認
        $sessionIDAfterLogin = session()->getId();
        $this->assertNotEquals($sessionIDBeforeLogin, $sessionIDAfterLogin);
    }

    /**
     * @test
     */
    public function ログイン成功時にログインログが記録される()
    {
        // ログイン処理
        $this->login();

        // ログが記録されているか確認
        $loginUser = Auth::guard('admin')->user();
        $loginLog = AdminLogin::where('admin_id', $loginUser->id)->first();
        $this->assertNotNull($loginLog);
        $this->assertSame($loginUser->id, $loginLog->admin_id);
    }
}
