<?php

namespace Tests\Feature\admin\Informations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Information;

class AdminInformationsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();

        Admin::factory()->create([
            'id' => 120001,
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

    $this ->information = Information::factory()->create([
            'title' => 'testtitle',
            'text'  => 'testtext',
            'admin_id' => '120001'
    ]);
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
     * お知らせ一覧画面へのアクセスが正常に行われるか確認
     */

    public function
    test_admin_informations_get_ok()
    {
        $this->login();
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
        $this->login();
        $informationData = [
            'title' => 'newtestTitle',
            'text' => 'newtestText',
        ];
        $response = $this -> post('/admin/informations',$informationData);
        $this -> assertDatabaseHas('information',[
            'title' => $informationData['title'],
            'text' => $informationData['text'],
        ]);
        $response -> assertRedirect(route('admin.informations.index'));
        $response->assertSessionHas('message', 'お知らせを登録しました');
    }

    /**
     * @test
     * お知らせの新規作成画面が正常に表示されているか確認
     */
    public function test_admin_informations_create_get_ok()
    {
        $this->login();
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
        $this->login();
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
        $this->login();
        $newTitle = 'NewTitle';
        $newText = 'NewText';
        $response = $this->patch("/admin/informations/{$this->information->id}",[
            'title' => $newTitle,
            'text' => $newText,
        ]);
        $response->assertRedirect(route('admin.informations.index'));
        $response->assertSessionHas('message', $newTitle.'を更新しました');
    }
        /**
     * @test
     * お知らせの削除が正常に行われるか確認
     */
    public function test_admin_informations_destroy_delete_ok()
    {
        $this->login();
        $response = $this->delete("/admin/informations/{$this->information->id}",[
            'id' => $this -> information -> id
        ]);
        $response->assertRedirect(route('admin.informations.index'));
        $this->assertDatabaseMissing( 'information', ['id' => $this->information->id,  'deleted_at' => null]);
        $response->assertSessionHas('danger', $this->information->title.'を削除しました');
    }
        /**
     * @test
     * お知らせの編集画面が正常に表示されるか確認
     */
    public function test_admin_informations_edit_get_ok()
    {
        $this->login();
        $response = $this->get("/admin/informations/{$this -> information ->id}/edit");
        $response->assertOk();
        $response->assertViewIs('admin.informations.edit');
    }
}
