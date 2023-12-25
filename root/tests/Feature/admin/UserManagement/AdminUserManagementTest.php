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

        // ランダムなユーザーIDを生成
        $randomUserId = rand();

        //モックデータを使用する
        $userData = [
            'username' => 'testUser',
            'password' => 'password1',
            'mail_address' => 'testUser1@user.com',
        ];

        //ユーザーIDを設定
        $userDate['id'] = $randomUserId;
        $response = $this->post('/admin/user-management', $userData);

        //データベースに対するアサーション
        $this->assertDatabaseHas('users', [
            'username' => 'testUser',
            'mail_address' => 'testUser1@user.com',
        ]);
        //パスワードのハッシュ検証
        $this->assertTrue(Hash::check('password1', User::where('username', 'testUser')->first()->password));

        $response->assertRedirect('/admin/user-management')->assertStatus(302)->assertSessionHas('message', 'testuserを登録しました');
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

         $response->assertJsonFragment(['name' => $this->user->username])
             ->assertJsonStructure([
                 'data' => [
                     '*' => [
                         'username',
                     ]
                 ],
             ]);
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

        $response = $this->delete("/admin/user-management/{$this->user->id}");
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        $this->assertDatabaseMissing( $this->user, ['id' => $this->user->id]);
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