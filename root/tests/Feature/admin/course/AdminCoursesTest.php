<?php

namespace Tests\Feature\Admin\course\AdminCourses;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Content;
use App\Models\Course;

class AdminCoursesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $course;
    private $content;

    // テスト用データの作成
    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'id' => 120001,
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        // ログイン
        $this->actingAs($this->admin, 'admin');

        $courses = [];
        for ($i = 1; $i <= 5; $i++) {
            $courses[] = Course::factory()->create([
                'id' => 200000 + $i,
                'title' => 'course_' . $i,
                'introduction' => 'これはテスト用のコースです。',
                'remarks' => 'This is test_course',
                'position' => $i,
            ]);
        }
        $this->course = $courses[0];

        $this->content = Content::factory()->create([
            'id' => 200000,
            'course_id' => $this->course->id,
            'admin_id' => $this->admin->id,
            'title' => 'test_content',
            'youtube_video_id' => '1q8VtH2zxYE',
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
        $response = $this->get('/admin/courses');

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * 該当コースのコンテンツの新規作成ができるか確認
     */
    public function test_admin_contents_store_post_ok()
    {
        $content = [
            'course_id' => $this->course->id,
            'admin_id' => $this->admin->id,
            'title'            => 'new_content',
            'youtube_video_id' => '2k9dh7SwEVs',
            'remarks'          => 'This is new_content',
        ];
        $response = $this->post("/admin/contents/{$this->course->id}", $content);
        $this->assertDatabaseHas('contents', [
            'course_id' => $this->course->id,
            'admin_id' => $this->admin->id,
            'title'            => $content['title'],
            'youtube_video_id' => $content['youtube_video_id'],
            'remarks'          => $content['remarks'],
        ]);
        $response->assertSessionHas('message', 'コンテンツを登録しました');
    }

    /**
     * @test
     * コース一覧画面へのアクセスが正常に行われるか確認
     */
    public function
    test_admin_courses_get_ok()
    {
        $response = $this->get('/admin/courses');
        $response->assertOk();
        $response->assertViewIs('admin.courses.index');
    }

    /**
     * @test
     * コースの新規作成がエラーなく成功するか確認
     */
    public function
    test_admin_courses_post_ok()
    {
        $course = [
            'title'       => 'newtestTitle',
            'introduction' => 'newtestIntroduction',
            'remarks'      => 'This is new_course',
        ];
        $response = $this->post('/admin/courses', $course);
        $this->assertDatabaseHas('courses', [
            'title'       => $course['title'],
            'introduction' => $course['introduction'],
            'remarks'      => $course['remarks'],
        ]);
        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas('message', 'コースを登録しました');
    }

    /**
     * @test
     * コースの新規作成画面が正常に表示されているか確認
     */
    public function test_admin_courses_create_get_ok()
    {
        $response = $this->get('/admin/courses/create');
        $response->assertOk();
        $response->assertViewIs('admin.courses.create');
    }

    /**
     * @test
     * コースの並べ替えが正常に行われるか確認
     */
    public function test_admin_courses_sort_post_ok()
    {
        $positions = [200003, 200001, 200005, 200004, 200002];
        $response = $this->postJson(route('admin.courses.sort'), ['positions' => $positions]);
        $sortedOrder = Course::orderBy('position')->pluck('id')->toArray();
        $expectedOrder = [200003, 200001, 200005, 200004, 200002];
        $this->assertEquals($expectedOrder, $sortedOrder);
        $response->assertJson(['message' => '並び替えを保存しました。']);
    }


    /**
     * @test
     * コースの更新が正常に行われるか確認
     */
    public function test_admin_courses_update_patch_ok()
    {
        $newTitle = 'NewTitle';
        $newIntroduction = 'NewIntroduction';
        $newRemarks = 'NewRemarks';
        $response = $this->patch("/admin/courses/{$this->course->id}", [
            'title'       => $newTitle,
            'introduction' => $newIntroduction,
            'remarks'      => $newRemarks,
        ]);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', [
            'id' => $this->course->id,
            'title'       => $newTitle,
            'introduction' => $newIntroduction,
            'remarks'      => $newRemarks,
        ]);
    }

    /**
     * @test
     * コースの削除が正常に行われるか確認
     */
    public function test_admin_courses_destroy_delete_ok()
    {
        $response = $this->delete("/admin/courses/{$this->course->id}", [
            'id' => $this->course->id
        ]);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertSoftDeleted('courses', ['id' => $this->course->id]);
        $this->assertNotNull($this->course->fresh()->deleted_at);
        $this->assertNotNull(session('danger'));
    }


    /**
     * @test
     * コースの編集画面が正常に表示されるか確認
     */
    public function test_admin_courses_edit_get_ok()
    {
        $response = $this->get("/admin/courses/{$this->course->id}/edit");
        $response->assertOk();
        $response->assertViewIs('admin.courses.edit');
    }
}
