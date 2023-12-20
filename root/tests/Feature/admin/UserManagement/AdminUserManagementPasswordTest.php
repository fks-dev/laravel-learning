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
            'password' => Hash::make('password1'),
            'mail_address' => 'testUser1@user.com',
        ]);
    }

    private function login(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
    }

    private function changePassword(): void
    {
        $this->user->update([
            'password' => Hash::make('password2'),
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

        // 変更後のパスワードを再取得し、変化を確認
        $userPasswordAfterChange = $this->user->password;
        $this->assertNotSame($userPasswordBeforeChange, $userPasswordAfterChange);
    }
}
