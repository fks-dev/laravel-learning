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


    /**
     * @test
     * ユーザー管理画面へのアクセスが正常に行われることを確認
     **/

     public function test_admin_user_management_index_ok()
    {
        $response = $this->get(route('admin.user-management.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.index');
    }

    /**
     * @test
     * ユーザーの新規作成がエラーなく成功することを確認
     */

    public function test_admin_user_management_post_ok()
    {
        $response = $this->post(route('admin.user-management.store'), $this->user);
        $response->assertRedirect(route('admin.user-management.index'));
    }

    /**
     * @test
     * ユーザーの新規作成が正常に行われることを確認
     */

    public function test_admin_user_management_create_get_ok()
    {
        $response = $this->get(route('admin.user-management.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.create');
    }

    /**
     * @test
     * 検索機能が正常に行われることを確認
     */

    public function test_admin_user_management_search_post_ok()
    {
        $response = $this->post(route('admin.user-management.search'), ['name' => $this->user->username]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $this->user->username]);
    }

     /**
     * @test
     * 更新が正常に行われることを確認
     */

    public function test_admin_user_management_update_patch_ok()
    {
        $newUsername = 'NewUsername';

        $response = $this->patch(route('admin.user-management.update', $this->user), ['username' => $newUsername]);
        $response->assertRedirect(route('admin.user-management.index'));
    }

       /**
     * @test
     * 削除が正常に行われることを確認
     */

      public function test_admin_user_management_destroy_delete_ok()
      {
        $response = $this->delete(route('admin.user-management.destroy', $this->user));
        $response->assertRedirect(route('admin.user-management.index'));
      }

        /**
     * @test
     * ユーザーの編集が正常に行われることを確認
     */
      public function test_admin_user_management_edit_get_ok()
      {
        $response = $this->get(route('admin.user-management.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.edit');
      }
}