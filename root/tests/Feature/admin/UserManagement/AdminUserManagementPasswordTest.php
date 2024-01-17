<?php

namespace Tests\Feature\Admin\UserManagement;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class AdminUserManagementPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;

    /**
     * テスト用のユーザー作成
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('old_password'),
            'mail_address' => 'testUser1@user.com',
        ]);
    }

    /**
     * パスワード変更メソッド
     */
    private function changePassword()
    {
        return $this->post("/admin/user-management/{$this->user->id}/password", [
            'password' => 'old_password',
            'new_password' => 'new_password',
            'new_password_confirmation' => 'new_password',
        ]);
    }

    /**
     * @test
     * 管理者がユーザーのパスワード管理画面に正常にアクセスできることを確認する
     */
    public function test_admin_user_management_password_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/user-management/{$this->user->id}/password");
        $response->assertOk();
    }

    /**
     * 管理者が未ログイン時にユーザーのパスワード管理画面にアクセスできないことを確認する
     * @test
     */
    public function test_admin_user_management_password_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/user-management/{$this->user->id}/password");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * ユーザーのパスワードが正常に変更されることを確認する
     */
    public function test_admin_user_management_password_post_ok_change_password()
    {
        $this->actingAs($this->admin, 'admin');
        $this->changePassword();

        //変更後のパスワードが期待される値になっているかの確認
        $this->assertTrue(Hash::check('new_password', $this->user->fresh()->password));
    }

    /**
     * @test
     * パスワード変更処理が完了するとユーザー管理画面へとリダイレクトすることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->changePassword();

        $response->assertRedirect('/admin/user-management/');
    }

    /**
     * @test
     * リダイレクト先のユーザー管理画面で「パスワードが変更されました」の表示が出力されることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect_message()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->changePassword();

        $response->assertSessionHas('message', 'パスワードが変更されました');
    }
}
