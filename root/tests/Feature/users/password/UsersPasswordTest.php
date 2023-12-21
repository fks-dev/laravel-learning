<?php

namespace Tests\Feature\users;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createTestUser();
    }

    private function createTestUser()
    {
        // ユーザーのテストデータを作成
        return User::factory()->create([
            'id' => 110001,
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUsers@User.com'
        ]);
    }

    /**
     * @test
     */
    public function test_users_password_get_ok_redirect_without_login()
    {
        // 未ログイン時のリダイレクトの確認
        $response = $this->get('/users/password');
        $response->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_password_get_ok_view()
    {
        // ログイン状態での、パスワード変更画面へのアクセスの確認
        $this->actingAs($this->user);
        $response = $this->get('/users/password');
        $response->assertViewIs('users.passwordChange.index');
    }

    /**
     * @test
     */
    public function test_users_password_post_ok_view()
    {
        // パスワード変更の動作確認
        $this->actingAs($this->user);
        $response = $this->post("/users/password/{$this->user->id}", [
            'password' => 'testUser',
            'new_password' => 'new_testUser',
            'new_password_confirmation' => 'new_testUser',
        ]);
        $response->assertRedirect('/users');
    }
}
