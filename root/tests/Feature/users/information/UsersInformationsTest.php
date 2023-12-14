<?php

namespace Tests\Feature\Users\Information;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@user.com',
        ]);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok()
    {
        $response = $this->actingAs($this->user)->get(route('users.informations.list'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_unauthenticated()
    {
        Auth::logout();

        $response = $this->get(route('users.informations.list'));

        $response->assertStatus(302)->assertRedirect(route('users.login.index'));
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_get_informations()
    {
        $user = User::factory()->create([
            'username' => 'testInfo',
            'password' => 'testInfo',
            'mail_address' => 'testInfo@test.com',
        ]);

        $group = Group::factory()->create(['group_name' => 'testInfo', ]);

        $information = Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 1,
        ]);

        $group->informations()->attach($information);

        Auth::login($user);

        $response = $this->get(route('users.informations.list'));

        $response->assertStatus(200)->assertSee('testInfo');
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_sort()
    {
        $user = User::factory()->create([
            'username' => 'testInfo',
            'password' => Hash::make('testInfo'),
            'mail_address' => 'testInfo@test.com',
        ]);

        Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 1,
            'updated_at' => now()->subDays(3),
        ]);

        Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 1,
            'updated_at' => now()->subDays(2),
        ]);

        Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => 1,
            'updated_at' => now()->subDays(1),
        ]);

        Auth::login($user);

        $response = $this->get(route('users.informations.list', ['sort' => 'updated_at']));

        $response->assertStatus(200)->assertSeeInOrder(['testInfo']);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_no_duplicates()
    {
        Information::factory()->create([
            'title' => 'testInfo1',
            'text' => 'testInfo1',
            'admin_id' => 1,
        ]);

        Information::factory()->create([
            'title' => 'testInfo2',
            'text' => 'testInfo2',
            'admin_id' => 1,
        ]);

        Information::factory()->create([
            'title' => 'testInfo3',
            'text' => 'testInfo3',
            'admin_id' => 1,
        ]);

        Auth::login($this->user);

        $response = $this->get(route('users.informations.list'));

        $response->assertStatus(200)
            ->assertDontSee('testInfo1')
            ->assertDontSee('testInfo2')
            ->assertDontSee('testInfo3');
    }



    /**
     * @test
     */
    public function test_users_informations_show_get_ok()
    {
        $user = User::factory()->create([
            'username' => 'testInfo',
            'password' => Hash::make('testInfo'),
            'mail_address' => 'testInfo@test.com',
        ]);

        $information = Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => $user->id,
        ]);

        Auth::login($user);

        $response = $this->get(route('users.informations.show', $information));

        //レスポンスが成功し、指定したテキストを含むことを確認
        $response->assertStatus(200)->assertSee('testInfo');
    }
}
