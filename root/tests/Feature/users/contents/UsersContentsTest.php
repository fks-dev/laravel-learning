<?php

namespace Tests\Feature\Users\Contents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Content;
use App\Models\Course;

class UsersContentsTest extends TestCase
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

    // users/contents/{course}にアクセスできる
    public function test_users_contents_get_ok()
    {
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/190001');
        $response->assertOk();
    }

    // users/contents/{course}にアクセス時、ログアウト中ならログインページにリダイレクトする
    public function test_users_contents_get_ok_unauthenticated()
    {
        $response = $this->get('/users/contents/190001');

        $response->assertRedirect(route('users.login.index'));
    }
    // 対応している公開コンテンツのタイトルがすべて表示されている
    public function test_users_contents_get_ok_all_title()
    {
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/190001');
        $course = Course::find(190001);
        $course->load('contents');
        $contentTitles = $course->contents->pluck('title');
        foreach ($contentTitles as $title) {
            $response->assertSee($title);
        }
    }
    // 非公開コンテンツが表示されていない
    public function test_users_contents_get_ok_hidden()
    {
        $this->actingAs($this->user);
        $course = Course::find(190001);
        $content = $course->contents->first();
        $content->update(
            [
                'is_public' => false,
            ]
        ); //対象レコードの内１つを非公開設定に変更
        $response = $this->get('/users/contents/190001');
        $response->assertDontSee($content->title);
    }
    // 各ソート番号(positionカラムの値)に適した順に表示されている
    public function test_users_contents_get_ok_sorted()
    {
        $course = Course::find(190001);
        $content = Content::find(200003);
        $content->update(
            [
                'position' => 10
            ]
        );
        $contents = $course->contents->sortBy('position');
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/190001');
        $response->assertSeeInOrder($contents->pluck('title')->toArray());
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
