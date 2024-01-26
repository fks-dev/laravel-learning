<?php

namespace Tests\Feature\Admin\course\AdminCourses;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Course;

class AdminCoursesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $course;

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

        for ($i = 1; $i <= 5; $i++) {
            Course::factory()->create([
                'id' => 190000 + $i,
                'title' => 'course_' . $i,
                'position' => $i,
            ]);
        }
        $this->course = Course::where('id', 190001)->first();
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
     * コース一覧画面へのアクセスが正常に行われるか確認
     */
    public function
    test_admin_courses_get_ok()
    {
        $response = $this->get('/admin/courses');
        $response->assertOk();
        // 事前に作成したコースが表示されるか確認
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('course_' . $i);
        };
        $response->assertViewIs('admin.courses.index');
    }

    /**
     * @test
     * コースが一覧画面でソート番号順に表示されていることを確認する
     */
    public function
    test_admin_courses_get_ok_sorted()
    {
        $response = $this->get('/admin/courses');
        $sortedOrder = Course::orderBy('position')->pluck('id')->toArray();
        $response->assertSeeInOrder($sortedOrder);
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
        ];
        $response = $this->post('/admin/courses', $course);
        $this->assertDatabaseHas('courses', [
            'title'       => $course['title'],
        ]);
        $response->assertRedirect('/admin/courses');
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
        $positions = [190003, 190001, 190005, 190004, 190002];
        $response = $this->postJson('/admin/courses/sort', ['positions' => $positions]);
        $sortedOrder = Course::orderBy('position')->pluck('id')->toArray();
        $expectedOrder = [190003, 190001, 190005, 190004, 190002];
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
        ]);
        $response->assertRedirect('/admin/courses');
        $this->assertDatabaseHas('courses', [
            'id' => $this->course->id,
            'title'       => $newTitle,
        ]);
        $response->assertSessionHas('message', $newTitle . 'を更新しました');
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
        $response->assertRedirect('/admin/courses');
        $this->assertSoftDeleted('courses', ['id' => $this->course->id]);
        $this->assertNotNull($this->course->fresh()->deleted_at);
        $response->assertSessionHas('danger', $this->course->title . 'を削除しました');
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
