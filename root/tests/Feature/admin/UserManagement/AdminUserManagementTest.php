<?php

namespace Tests\Feature\Admin\UserManagement;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class AdminUserManagementTest extends TestCase
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
     * ユーザー管理画面へのアクセスが正常に行われることを確認
     **/

     public function test_admin_user_management_index_ok()
    {
        $this->login();
        $response = $this->get('/admin/user-management');
        $response->assertStatus(200);

        $response->assertViewIs('admin.user-management.index');
    }

    /**
     * @test
     * ユーザーの新規作成がエラーなく成功することを確認
     */

    public function test_admin_user_management_post_ok()
    {
        $this->login();

         //モックデータを使用する
         $userData = [
            'username' => 'newtestUser',
            'password' => 'newpassword1',
            'mail_address' => 'newtestUser1@user.com',
        ];


        // storeメソッドを呼び出してユーザーを作成
        $response = $this->post('/admin/user-management', $userData);

        // データベースにユーザーが作成されたか確認
        $this->assertDatabaseHas('users', [
            'username' => $userData['username'],
            'mail_address' => $userData['mail_address'],
        ]);

        // パスワードがHash化されているか確認
        $user = User::where('username', $userData['username'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        // リダイレクトを確認
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        // セッションにメッセージが保存されているか確認
        $this->assertNotNull(session('message'));

        // セッションのメッセージが期待通りのものか確認
        $this->assertEquals($userData['username'] . 'を登録しました', session('message'));
    }


    /**
     * @test
     * ユーザーの新規作成画面が正常に表示されることを確認
     */

    public function test_admin_user_management_create_get_ok()
    {
        $this->login();

        $response = $this->get('/admin/user-management/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.create');
    }

    /**
     * @test
     * 検索機能が正常に行われることを確認
     */


     public function test_admin_user_management_search_post_ok()
     {
        $this->login();

         $response = $this->post('/admin/user-management/search', ['name' => $this->user->username]);

         // 正しいJSON構造を持っていることを確認
         $response->assertJsonStructure([
            '*' => [
                'id',
                'username',
                'mail_address',
                'deleted_at',
                'created_at',
                'updated_at',
                ],
            ]);

        // 正しいデータが含まれていることを確認
        $response->assertJsonFragment([
            'id' => $this->user->id,
            'username' => $this->user->username,
            'mail_address' => $this->user->mail_address,
        ]);

        $response->assertStatus(200);
     }

     /**
     * @test
     * 更新が正常に行われることを確認
     */

    public function test_admin_user_management_update_patch_ok()
    {
        $this->login();

        $newUsername = 'NewUsername';
        $newMailaddress = 'newtestUser1@user.com';

        $response = $this->patch("/admin/user-management/{$this->user->id}" , [
            'username' => $newUsername,
            'mail_address' => $newMailaddress,
        ]);
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'username' => $newUsername,
            'mail_address' => $newMailaddress,
        ]);
    }

       /**
     * @test
     * 削除が正常に行われることを確認
     */

      public function test_admin_user_management_destroy_delete_ok()
      {
        $this->login();

        $response = $this->delete("/admin/user-management/{$this->user->id}", ['id' => $this->user->id]);
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        $this->assertDatabaseMissing( 'users', ['id' => $this->user->id,  'deleted_at' => null]);
      }

        /**
     * @test
     * ユーザーの編集画面が正常に表示されることを確認
     */
      public function test_admin_user_management_edit_get_ok()
      {
        $this->login();

        $response = $this->get("/admin/user-management/{$this->user->id}/edit");

        $response->assertStatus(200);
        $response = $this->get('/admin/user-management/edit');
      }
}