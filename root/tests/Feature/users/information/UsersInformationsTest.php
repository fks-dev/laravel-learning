<?php

namespace Tests\Feature\Users\Information;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Group;
use App\Models\Information;


class UsersInformationsTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'testInfo',
            'password' => Hash::make('testInfo'),
            'mail_address' => 'testInfo@test.com',
        ]);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok()
    {
        // ユーザーがログインして一覧を取得するリクエストを作成
        $response = $this->actingAs($this->user)->get(route('users.informations.list'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で一覧を取得する
        $response = $this->get(route('users.informations.list'));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('users.login.index'));
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_get_informations()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup' ]);
        $this->user->groups()->attach($group);

        // テスト用のお知らせを作成し、グループと関連付ける
        $information = Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 120001,
        ]);
        $group->informations()->attach($information);

        // お知らせの一覧を取得し、特定のお知らせが表示されていることを確認する
        $response = $this->actingAs($this->user)->get(route('users.informations.list'));
        $response->assertStatus(200)->assertSee('testInfo');
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_sort()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

         // 3つのお知らせを作成し、更新日時を設定してグループと関連付ける
        for ($i = 1; $i <= 3; $i++)
        {
            $information = Information::factory()->create([
                'title' => 'Information' . $i,
                'text' => 'InformationText',
                'admin_id' => 120001,
                'updated_at' => now()->subDays($i),
            ]);
            $group->informations()->attach($information->id);
        }

        // お知らせの一覧を取得し、更新日時の順にお知らせが並んでいるか確認
        $response = $this->actingAs($this->user)->get(route('users.informations.list'));
        $response->assertSeeInOrder(['Information1', 'Information2' ,'Information3']);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_no_duplicates()
    {
        // 2つのグループを作成し、ユーザーをそれぞれに関連付ける
        for ($i = 1; $i <= 2; $i++)
        {
            $group = Group::factory()->create(['group_name' => 'testGroup' . $i]);
            $this->user->groups()->attach($group->id);

            // お知らせを作成し、各グループに関連付ける
            $information = Information::factory()->create([
                'title' => 'Information',
                'text' => 'InformationText',
                'admin_id' => 120001,
            ]);
            $group->informations()->attach($information->id);
        }

        // お知らせの一覧を取得し、同じお知らせが重複しないことを確認
        $response = $this->actingAs($this->user)->get(route('users.informations.list'));
        $response->assertSee('Information');
        $this->assertEquals(2, substr_count($response->getContent(), 'Information'));
    }



    /**
     * @test
     */
    public function test_users_informations_show_get_ok_()
    {
        $information = Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('users.informations.show', $information));

        //レスポンスが成功し、指定したテキストを含むことを確認
        $response->assertStatus(200)->assertSee('testInfo');
    }
}
