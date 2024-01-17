<?php

namespace Tests\Feature\admin\information;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Information;

class AdminInformationsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $information;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        // ログイン
        $this->actingAs($this->admin, 'admin');

        $this->information = Information::factory()->create([
            'title' => 'testtitle',
            'text'  => 'testtext',
            'admin_id' => '120001'
        ]);
    }

    /**
     * @test
     * 未ログイン時にログイン画面へ遷移するか確認
     */
    public function
    test_unauthenticated_user_redirected_to_login()
    {
        // ログアウトして未ログイン状態にする
        auth()->logout();

        // 未ログインの状態でアクセス
        $response = $this->get('/admin/informations');

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * お知らせ一覧画面へのアクセスが正常に行われるか確認
     */
    public function
    test_admin_informations_get_ok()
    {
        $response = $this->get('/admin/informations');
        $response->assertOk();
        $response->assertViewIs('admin.informations.index');
    }

    /**
     * @test
     * お知らせの新規作成がエラーなく成功するか確認
     */
    public function test_admin_informations_post_ok()
    {
        $information = [
            'title' => 'newtestTitle',
            'text' => 'newtestText',
        ];
        $response = $this->post('/admin/informations', $information);
        $this->assertDatabaseHas('information', [
            'title' => $information['title'],
            'text' => $information['text'],
        ]);
        $response->assertRedirect('/admin/informations/');
        $response->assertSessionHas('message', 'お知らせを登録しました');
    }

    /**
     * @test
     * お知らせの新規作成画面が正常に表示されているか確認
     */
    public function test_admin_informations_create_get_ok()
    {
        $response = $this->get('/admin/informations/create');
        $response->assertOk();
        $response->assertViewIs('admin.informations.create');
    }

    /**
     * @test
     * 作成したお知らせが表示されるか確認
     */
    public function test_admin_informations_show_get_ok()
    {
        $response = $this->get("/admin/informations/{$this->information->id}");
        $response->assertOk();
        $response->assertViewIs('admin.informations.show');
    }

    /**
     * @test
     * お知らせの更新が正常に行われるか確認
     */
    public function test_admin_informations_update_patch_ok()
    {
        $newTitle = 'NewTitle';
        $newText = 'NewText';
        $response = $this->patch("/admin/informations/{$this->information->id}", [
            'title' => $newTitle,
            'text' => $newText,
        ]);
        $response->assertRedirect('/admin/informations/');
        $this->assertDatabaseHas('information', [
            'id' => $this->information->id,
            'title' => $newTitle,
            'text' => $newText,
        ]);
        $this->assertNotNull(session('message'));
    }

    /**
     * @test
     * お知らせの削除が正常に行われるか確認
     */
    public function test_admin_informations_destroy_delete_ok()
    {
        $response = $this->delete("/admin/informations/{$this->information->id}", [
            'id' => $this->information->id
        ]);
        $response->assertRedirect('/admin/informations/');
        $this->assertSoftDeleted('information', ['id' => $this->information->id]);
        $this->assertNotNull($this->information->fresh()->deleted_at);
        $this->assertNotNull(session('danger'));
    }

    /**
     * @test
     * お知らせの編集画面が正常に表示されるか確認
     */
    public function test_admin_informations_edit_get_ok()
    {
        $response = $this->get("/admin/informations/{$this->information->id}/edit");
        $response->assertOk();
        $response->assertViewIs('admin.informations.edit');
    }
}
