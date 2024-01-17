<?php

namespace Tests\Feature\Admin\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
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
    public function test_admin_login_get_ok()
    {
        $response = $this->get('/admin/login');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_redirect()
    {
        $response = $this->login();

        // リダイレクトの確認
        $response->assertRedirect('/admin/admin-management');
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_authenticated_as_expected_user()
    {
        $this->login();

        // 認証されたユーザーが期待するユーザー名を持っているか確認
        $this->assertAuthenticatedAs(Admin::where('username', 'testAdmin')->first(), 'admin');
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_session_regenerated()
    {
        // セッションIDの保持
        $sessionIDBeforeLogin = session()->getId();

        $this->login();

        // セッションIDを再取得し、変化を確認
        $sessionIDAfterLogin = session()->getId();
        $this->assertNotSame($sessionIDBeforeLogin, $sessionIDAfterLogin);
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_admin_log()
    {
        $this->login();

        // ログが記録されているか確認
        $loginUser = Auth::guard('admin')->user();
        $this->assertDatabaseHas('admin_logs', ['admin_id' => $loginUser->id]);
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok()
    {
        $this->login();
        $this->logout();

        //ユーザーがゲスト状態（ログアウト状態）であるか確認
        $this->assertGuest('admin');
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok_redirect()
    {
        $this->login();
        $response = $this->logout();

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok_session_invalidated()
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
    public function test_admin_login_delete_ok_session_regenerate()
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
