<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLogin;

class AdminLoginTest extends TestCase
{
    public function testAdminLoginSuccess()
    {
        // ログインページにアクセス
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        // セッションIDの保持
        $sessionIDBeforeLogin = session()->getId();

        // ログイン処理
        $response = $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);

        // リダイレクトの確認
        $response->assertRedirect('/admin/admin-management');

        // セッションIDの変化を確認
        $sessionIDAfterLogin = session()->getId();
        $this->assertNotEquals($sessionIDBeforeLogin, $sessionIDAfterLogin);

        // 認証されたユーザーが期待するユーザー名を持っているか確認
        $loginUser = Auth::guard('admin')->user();
        $this->assertEquals('testAdmin', $loginUser->username);

        // ログが記録されているか確認
        $loginLog = AdminLogin::where('admin_id', $loginUser->id)->first();
        $this->assertNotNull($loginLog);
        $this->assertEquals($loginUser->id, $loginLog->admin_id);
    }
}
