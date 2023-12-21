<?php

namespace Tests\Feature\Users\Contents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Content;
use App\Models\ContentsLog;
use App\Models\Course;

class UsersContentsLogTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->create_user();
        $this->create_course();
        $this->create_content();
    }

    private function create_user()
    {
        User::factory()->create([
            'id' => 110001,
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@user.com',
        ]);
    }

    private function create_course()
    {
        Course::factory()->create([
            'id' => 190001,
            'title' => 'test_course',
            'introduction' => 'これはテスト用のコースです。',
            'remarks' => 'This is test_course'
        ]);
    }

    private function create_content()
    {
        for ($i = 1; $i <= 5; $i++) {
            Content::factory()->create([
                'id' => 200000 + $i,
                'title' => ('content_' . $i),
                'admin_id' => 120001,
                'course_id' => 190001,
                'youtube_video_id' => '1q8VtH2zxYE',
                'is_public' => True,
            ]);
        }
    }

    // users/contents/view/{content}にPOSTメソッドでアクセスできる
    public function test_users_contents_view_post_ok()
    {
        $this->actingAs($this->user);
        $response = $this->post('/users/contents/view/200001',[
            'log' => 0
        ]);
        $response->assertStatus(302);
    }

    public function test_users_contents_view_post_ok_unauthenticated()
    {
        $response = $this->post('/users/contents/view/200001',[
            'log' => 0
        ]);
        $response->assertRedirect(route('users.login.index'));
    }


    //はじめて閲覧したとき閲覧記録が１つ作成される
    public function test_users_contents_view_post_ok_create_log()
    {
        $this->actingAs($this->user);
        $this->post('/users/contents/view/200001',[
            'log' => 0
        ]);
        $this->assertDatabaseHas('contents_logs', [
            'content_id' => 200001,
        ]);
    }
    //対応するコンテンツ一覧にリダイレクトされる
    public function test_users_contents_view_post_ok_redirect_index()
    {
        $this->actingAs($this->user);
        $response = $this->post('/users/contents/view/200001',[
            'log' => 0
        ]);
        $response->assertRedirect(route('users.contents.index',190001));
    }
    //保存された閲覧記録が表示されている
    public function test_users_contents_view_get_display_created_log()
    {
        $this->actingAs($this->user);
        $this->post('/users/contents/view/200001',[
            'log' => 0
        ]);
        $log = ContentsLog::where('content_id',200001)->first();
        $response = $this->get('/users/contents/190001');
        $response->assertSee($log->created_at);
    }
    //閲覧済みコンテンツを再度閲覧し最終学習日時を更新する
    public function test_users_contents_view_post_ok_update_log()
    {
        ContentsLog::create([
            'user_id' => 110001,
            'content_id' => 200001,
            'completed' => 0,
            'created_at' => '2023-09-01 01:23:45',
            'updated_at' => '2023-09-01 01:23:45',
        ]);
        $this->actingAs($this->user);
        $this->post('/users/contents/view/200001',[
            'log' => 1
        ]);
        $log = ContentsLog::first();
        $this->assertEquals($log->completed,1); //更新処理が実行されたことを確認
        $this->assertTrue($log->created_at < $log->updated_at);
    }
    //更新された閲覧記録が表示されている
    public function test_users_contents_view_get_ok_display_updated_log()
    {
        ContentsLog::create([
            'user_id' => 110001,
            'content_id' => 200001,
            'completed' => False,
            'created_at' => '2023-09-01 01:23:45',
            'updated_at' => '2023-09-01 01:23:45',
        ]);
        $this->actingAs($this->user);
        $this->post('/users/contents/view/200001',[
            'log' => 1
        ]);
        $log = ContentsLog::first();
        $response = $this->get('/users/contents/190001');
        $response->assertSeeInOrder([$log->created_at,$log->updated_at,'☑']);
    }



}