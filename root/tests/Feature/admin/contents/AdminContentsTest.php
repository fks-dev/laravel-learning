<?php

namespace Tests\Feature\Admin\contents\AdminContents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Content;
use App\Models\Course;

class AdminContentsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $course;
    private $content;

    /**
     * テスト用データを作成
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'id' => 120001,
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        $this->course = Course::factory()->create([
            'id' => 190001,
            'title' => 'test_course',
            'introduction' => 'これはテスト用のコースです。',
            'remarks' => 'This is test_course'
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Content::factory()->create([
                'id' => 200000 + $i,
                'title' => 'content_' . $i,
                'admin_id' => $this->admin->id,
                'course_id' => $this->course->id,
                'youtube_video_id' => '1q8VtH2zxYE',
                'position' => $i,
            ]);
        }

        $this->content = Content::where('course_id', $this->course->id)->first();
    }

    /**
     * コンテンツの新規登録メソッド
     */
    public function storeContent()
    {
        return $this->post("/admin/contents/190001", [
            'course_id'        => $this->course->id,
            'admin_id'         => $this->admin->id,
            'title'            => 'new_content',
            'youtube_video_id' => '2k9dh7SwEVs',
            'remarks'          => 'This is new_content',
        ]);
    }

    /**
     * コンテンツの編集メソッド
     */
    public function editContent()
    {
        return $this->patch("/admin/contents/{$this->content->id}", [
            'admin_id' => $this->admin->id,
            'course_id' => '190002',
            'title' => 'content_1_update',
            'youtube_video_id' => '1q8VtUpdate',
            'remarks' => 'This is update_content',
        ]);
    }

    /**
     * コンテンツの複製メソッド
     */
    public function duplicateContent()
    {
        return $this->post("/admin/contents/{$this->content->id}/duplicate");
    }

    /**
     * コンテンツの論理削除メソッド
     */
    public function destroyContent()
    {
        return $this->delete("/admin/contents/{$this->content->id}");
    }

    /**
     * コンテンツの並べ替えメソッド
     */
    public function sortContent()
    {
        $positions = [200003, 200001, 200005, 200004, 200002];
        return $this->postJson("/admin/contents/sort", ['positions' => $positions]);
    }

    /**
     * @test
     * 管理者が該当コースのコンテンツの一覧画面に正常にアクセスできることを確認する
     */
    public function test_admin_contents_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/contents/190001");
        $response->assertOk();
    }

    /**
     * @test
     * コンテンツが一覧画面でソート番号順に表示されていることを確認する
     */
    public function test_admin_contents_get_ok_sorted()
    {
        $this->actingAs($this->admin, 'admin');
        $contents = Content::where('course_id', $this->course->id)->orderBy('position')->get();

        $response = $this->get("/admin/contents/190001");
        $response->assertSeeInOrder($contents->pluck('position')->toArray());
    }

    /**
     * @test
     * 管理者が未ログイン時に該当コースのコンテンツの一覧画面にアクセスできないことを確認する
     */
    public function test_admin_contents_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/contents/190001");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 管理者がコンテンツの詳細画面に正常にアクセスできることを確認する
     */
    public function test_admin_contents_show_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/contents/{$this->content->id}/show");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にコンテンツの詳細画面にアクセスできないことを確認する
     */
    public function test_admin_contents_show_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/contents/{$this->content->id}/show");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 管理者が該当コースのコンテンツの新規登録画面に正常にアクセスできることを確認する
     */
    public function test_admin_contents_create_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/contents/create/190001");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にコンテンツの新規登録画面にアクセスできないことを確認する
     */
    public function test_admin_contents_create_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/contents/create/190001");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 管理者が該当コースにコンテンツを新規登録できることを確認する
     */
    public function test_admin_contents_create_post_ok()
    {
        $this->actingAs($this->admin, 'admin');
        $this->storeContent();

        // データベース上で新しいコンテンツが追加されたか確認
        $this->assertDatabaseHas('contents', [
            'course_id'        => $this->course->id,
            'admin_id'         => $this->admin->id,
            'title'            => 'new_content',
            'youtube_video_id' => '2k9dh7SwEVs',
            'remarks'          => 'This is new_content',
        ]);
    }

    /**
     * @test
     * コンテンツの新規登録が完了すると、該当コースのコンテンツ一覧画面へリダイレクトし、リダイレクト先で「コンテンツを登録しました」の表示が出力されることを確認する
     */
    public function test_admin_contents_create_post_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->storeContent();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/contents/190001");

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'コンテンツを登録しました');
    }

    /**
     * @test
     * 管理者がコンテンツの編集画面に正常にアクセスできることを確認する
     */
    public function test_admin_contents_edit_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/contents/{$this->content->id}/edit");
        $response->assertOk();
    }

    /**
     * @test
     * 管理者が未ログイン時にコンテンツの編集画面にアクセスできないことを確認する
     */
    public function test_admin_contents_edit_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/contents/{$this->content->id}/edit");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 管理者がコンテンツを編集できることを確認する
     */
    public function test_admin_contents_edit_patch_ok()
    {
        $this->actingAs($this->admin, 'admin');
        $this->editContent();

        //データベース上でコンテンツが更新されたか確認
        $this->assertDatabaseHas('contents', [
            'admin_id' => $this->admin->id,
            'course_id' => '190002',
            'title' => 'content_1_update',
            'youtube_video_id' => '1q8VtUpdate',
            'remarks' => 'This is update_content',
        ]);
    }

    /**
     * @test
     * コンテンツの編集が完了すると、該当コースのコンテンツ一覧画面へリダイレクトし、リダイレクト先で「コンテンツを変更しました」の表示が出力されることを確認する
     */
    public function test_admin_contents_edit_patch_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->editContent();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/contents/190001");

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'コンテンツを変更しました');
    }

    /**
     * @test
     * 管理者がコンテンツを複製できることを確認する
     */
    public function test_admin_contents_duplicate_post_ok()
    {
        $this->actingAs($this->admin, 'admin');
        $this->duplicateContent();

        //複製したコンテンツと同じタイトルのコンテンツをすべて取得
        $sameTitleContents = Content::where('title', $this->content->title)->get();
        // 同じタイトルのコンテンツが2つあるか確認
        $this->assertTrue($sameTitleContents->count() === 2);
    }

    /**
     * @test
     * コンテンツの複製が完了すると、該当コースのコンテンツ一覧画面へリダイレクトし、リダイレクト先で「コンテンツを複製しました」の表示が出力されることを確認する
     */
    public function test_admin_contents_duplicate_post_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->duplicateContent();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/contents/190001");

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('message', 'コンテンツを複製しました。');
    }

    /**
     * @test
     * 管理者がコンテンツを論理削除できることを確認する
     */
    public function test_admin_contents_delete_ok()
    {
        $this->actingAs($this->admin, 'admin');
        $this->destroyContent();

        //データベース上でコンテンツが論理削除されたか確認
        $this->assertSoftDeleted('contents', [
            'id' => 200001,
            'title' => 'content_1',
            'admin_id' => $this->admin->id,
            'course_id' => $this->course->id,
            'youtube_video_id' => '1q8VtH2zxYE',
        ]);

        //deleted_atカラムが適切に設定されているか確認
        $this->assertNotNull($this->content->fresh()->deleted_at);
    }

    /**
     * @test
     * コンテンツの削除が完了すると、該当コースのコンテンツ一覧画面へリダイレクトし、リダイレクト先で「コンテンツを削除しました」の表示が出力されることを確認する
     */
    public function test_admin_contents_delete_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->destroyContent();

        //正しいリダイレクトが行われているか確認
        $response->assertRedirect("/admin/contents/190001");

        //正しいセッションメッセージが表示されているか確認
        $response->assertSessionHas('danger', $this->content->title . 'を削除しました');
    }

    /**
     * @test
     * 管理者がコンテンツを並べ替えできることを確認する
     */
    public function test_admin_contents_sort_post_ok()
    {
        $this->actingAs($this->admin, 'admin');
        $this->sortContent();

        // 並び替え後のコンテンツの順序を取得
        $sortedOrder = Content::where('course_id', $this->course->id)->orderBy('position')->pluck('id')->toArray();

        // 並び替え後の順序が期待される順序と一致するか確認
        $expectedOrder = [200003, 200001, 200005, 200004, 200002];
        $this->assertEquals($expectedOrder, $sortedOrder);
    }

    /**
     * @test
     * コンテンツの並べ替えが完了すると、該当コースのコンテンツ一覧画面で「並び替えを保存しました」の表示が出力されることを確認する
     */
    public function test_admin_contents_sort_post_ok_message()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->sortContent();

        $response->assertJson(['message' => '並び替えを保存しました。']);
    }

    /**
     * コンテンツ新規作成_正常系バリデーションチェック
     */
    public function data_admin_contents_create_post_and_patch_ok_validation_ok()
    {
        return [
            //基本系
            'basic' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => 'basic',
                    'remarks'          => 'basic',
                ]
            ],
            //course_id最大値
            'max_number_of_title' => [
                'data' => [
                    'course_id'        => 199999,
                    'admin_id'         => 120001,
                    'title'            => 'a',
                    'youtube_video_id' => 'MaxNumOfTitle',
                    'remarks'          => 'max_number_of_title',
                ]
            ],
            //admin_id最大値
            'max_number_of_title' => [
                'data' => [
                    'course_id'        => 199999,
                    'admin_id'         => 129999,
                    'title'            => 'a',
                    'youtube_video_id' => 'MaxNumOfTitle',
                    'remarks'          => 'max_number_of_title',
                ]
            ],
            //title最小文字数
            'max_number_of_title' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'a',
                    'youtube_video_id' => 'MaxNumOfTitle',
                    'remarks'          => 'max_number_of_title',
                ]
            ],
            //title最大文字数
            'max_number_of_title' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => str_pad("Title", 255, "a"),
                    'youtube_video_id' => 'MaxNumOfTitle',
                    'remarks'          => 'max_number_of_title',
                ]
            ],
            //YouTubeVideoId最小文字数
            'max_number_of_youtube_video_id' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => 'a',
                    'remarks'          => 'max_number_of_youtube_video_id',
                ]
            ],
            //YouTubeVideoId最大文字数
            'max_number_of_youtube_video_id' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => str_pad("YouTubeVideoId", 255, "a"),
                    'remarks'          => 'max_number_of_youtube_video_id',
                ]
            ],
            //remarks最小文字数
            'max_number_of_remarks' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => 'MaxNumOfRemarks',
                    'remarks'          => 'a',
                ]
            ],
            //remarks最大文字数
            'max_number_of_remarks' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => 'MaxNumOfRemarks',
                    'remarks'          => str_repeat("a", 500),
                ]
            ],
            //remarksなし
            'missing_remarks' => [
                'data' => [
                    'course_id'        => 190001,
                    'admin_id'         => 120001,
                    'title'            => 'Validation Test',
                    'youtube_video_id' => 'MissingRemarks',
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_contents_create_post_and_patch_ok_validation_ok
     * コンテンツ新規作成時のバリデーションチェック(正常系)
     */
    public function test_admin_contents_create_post_ok_validation_ok($data)
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->post("/admin/contents/190001", $data);

        $response->assertSessionHasNoErrors();
    }
}
