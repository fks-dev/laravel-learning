<?php

namespace Tests\Feature\Users\Contents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Content;
use App\Models\Course;

class UsersContentsViewTest extends TestCase
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

    // users/contents/view/{content}にアクセスできる
    public function test_users_contents_view_get_ok()
    {
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/view/200001');
        $response->assertOk();
    }

    // users/contents/view/{content}にアクセス時、ログアウト中ならログインページにリダイレクトする
    public function test_users_contents_view_get_ok_unauthenticated()
    {
        $response = $this->get('/users/contents/view/200001');
        $response->assertRedirect(route('users.login.index'));
    }
    // 非公開コンテンツへのアクセス時、ユーザートップ画面にリダイレクトする
    public function test_users_contents_view_get_ok_hidden()
    {
        $content = Content::find(200001);
        $content->update(
            [
                'is_public' => False,
            ]
        );
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/view/200001');
        $response->assertRedirect(route('users.index'));
    }

    private function create_user()
    {
        User::factory()->create([
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
}
