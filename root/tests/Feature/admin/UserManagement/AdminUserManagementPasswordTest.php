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

    public function setUp(): void
    {
        parent::setUp();

        Admin::factory()->create([
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
     * ログインメソッド
     */
    private function login(): void
    {
        $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
    }

    /**
     * パスワード変更メソッド
     */
    private function changePassword()
    {
        return $this->post(route('admin.user-management.password', ['user' => $this->user->id]), [
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
        $this->login();

        $response = $this->get(route('admin.user-management.password', ['user' => $this->user->id]));
        $response->assertOk();
    }

    /**
     * @test
     * ユーザーのパスワードが正常に変更されることを確認する
     */
    public function test_admin_user_management_password_post_ok_change_password()
    {
        $this->login();

        // 変更前のパスワードの保持
        $userPasswordBeforeChange = $this->user->password;

        $this->changePassword();

        // 変更後のパスワードを取得し、変化を確認
        $userPasswordAfterChange = $this->user->fresh()->password;
        $this->assertNotSame($userPasswordBeforeChange, $userPasswordAfterChange);
    }

    /**
     * @test
     * パスワード変更処理が完了するとユーザー管理画面へとリダイレクトすることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect()
    {
        $this->login();
        $response = $this->changePassword();

        $response->assertRedirect(route('admin.user-management.index'));
    }

    /**
     * @test
     * リダイレクト先のユーザー管理画面で「パスワードが変更されました」の表示が出力されることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect_message()
    {
        $this->login();
        $this->changePassword();

        $response = $this->get(route('admin.user-management.index'));
        $response->assertSee('パスワードが変更されました');
    }
}
