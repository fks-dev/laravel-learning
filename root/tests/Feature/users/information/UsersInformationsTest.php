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

    /**
     * @test
     */
    public function test_users_informations_list_get_ok()
    {
        $user = User::factory()->create([
            'username' => 'testInfo',
            'password' => Hash::make('testInfo'),
            'mail_address' => 'testInfo@test.com',
        ]);

        $group = Group::factory()->create(['group_name' => 'testInfo', ]);

        $information = Information::factory()->create([
            'title' => 'testInfo',
            'text' => 'testInfo',
            'admin_id' => $user->id,
        ]);

        $group->informations()->attach($information);

        $response = $this->actingAs($user)->get(route('users.informations.list'));

        //レスポンスが成功し、指定したテキストを含むことを確認
        $response->assertStatus(200)->assertSee('testInfo');
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
