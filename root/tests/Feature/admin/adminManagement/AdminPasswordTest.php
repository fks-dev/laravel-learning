<?php

namespace Tests\Feature\Admin\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
    }

    private function createAdmin()
    {
        Admin::factory()->create([
            'id' => 120001,
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);
    }

    private function postNewPassword()
    {
        $response = $this->post('/admin/admin-management/120001/password', [
            'password' => 'testAdmin',
            'new_password' => 'changedPassword',
            'new_password_confirmation' => 'changedPassword'
        ]);
        return $response;
    }

    private function login()
    {
        $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_get_ok()
    {
        //パスワード変更画面にアクセスできる
        $this->login();
        $response = $this->get('/admin/admin-management/120001/password');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_get_ok_unauthenticated()
    {
        //ログアウト時にパスワード変更画面にアクセスしたときログインページにリダイレクトする
        $response = $this->get('/admin/admin-management/120001/password');
        $response->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok()
    {
        //パスワード変更後に管理者ログイン画面にリダイレクトされる
        $this->login();
        $response = $this->postNewPassword();
        $response->assertRedirect(route('admin.admin-management.index'));
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok_db_change()
    {
        //データベース内のデータが変更されている
        $this->login();
        $this->postNewPassword();
        $hashedPassword = Admin::find(120001)->password;
        $this->assertTrue(Hash::check('changedPassword', $hashedPassword));
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok_login()
    {
        //新しいパスワードでログインし、管理者ログイン画面にアクセスできる
        $this->login();
        $response = $this->postNewPassword();
        $this->delete('admin/login'); //ログアウト
        $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'changedPassword',
        ]); //新しいパスワードでログイン
        $response = $this->get('/admin/admin-management');
        $response->assertOk(); //アドミン管理画面にアクセスできる
    }
}