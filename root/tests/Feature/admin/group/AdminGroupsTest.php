<?php

namespace Tests\Feature\Admin\Group;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;

class AdminGroupsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@test.com',
        ]);
    }

    // ユーザーのテストデータを作成
    private function createTestUsers()
    {
        return User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@test.com',
        ]);
    }

    // グループのテストデータを作成
    private function createTestGroups()
    {
        return Group::factory()->create([
            'group_name' => 'Group',
            'remarks' => 'GroupRemark',
        ]);
    }

    //コンテンツのテストデータを作成
    private function createTestCourses()
    {
        return Course::factory()->create([
            'title' => 'Course',
            'introduction' => 'CourseIntro',
            'remarks' => 'CourseRemark',
        ]);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok()
    {
        // 管理者としてログインし、一覧画面を取得する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で一覧を取得する
        $response = $this->get(route('admin.groups.index'));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_groups_index_view()
    {
        // 管理者としてログインし、viewが正しく表示されることを確認する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.index'));
        $response->assertViewIs('admin.groups.index');
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_get_sort_created()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $course = $this->createTestCourses();

        // 3つのグループとユーザーを作成し、作成日時を設定する
        for ($i = 1; $i <= 3; $i++)
        {
            $group = Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'created_at' => now()->subDays($i),
            ]);

            $user = User::factory()->create([
                'username' => 'User' . $i,
                'password' => Hash::make('testPass'),
                'mail_address' => 'User' . $i . '@test.com',
            ]);

            // グループとユーザー、グループとコースを関連付ける
            $user->groups()->attach($group);
            $group->courses()->attach($course);
        }

        // グループ一覧を取得し、作成日時の順にグループが並んでいるか確認する
        $response = $this->get(route('admin.groups.index'));
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_get_sort_updated()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $course = $this->createTestCourses();

        // 3つのグループとユーザーを作成し、更新日時を設定する
        for ($i = 1; $i <= 3; $i++)
        {
            $group = Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'updated_at' => now()->subDays($i),
            ]);

            $user = User::factory()->create([
                'username' => 'User' . $i,
                'password' => Hash::make('testPass'),
                'mail_address' => 'User' . $i . '@test.com',
            ]);

            // グループとユーザー、グループとコースを関連付ける
            $user->groups()->attach($group);
            $group->courses()->attach($course);
        }

        // グループ一覧を取得し、更新日時の順にグループが並んでいるか確認する
        $response = $this->get(route('admin.groups.index'));
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_no_duplicates()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        // 2つのユーザーとコースを作成する
        for ($i = 1; $i <= 2; $i++)
        {
            $user = User::factory()->create([
                'username' => 'User' . $i,
                'password' => Hash::make('testPass'),
                'mail_address' => 'User' . $i . '@test.com',
            ]);

            $course = Course::factory()->create([
                'title' => 'Course' . $i,
                'introduction' => 'CourseIntro',
                'remarks' => 'CourseRemark',
            ]);

            // グループとユーザー、グループとコースを関連付ける
            $user->groups()->attach($group);
            $group->courses()->attach($course);

            // グループの一覧を取得し、重複しないことを確認
            $response = $this->get(route('admin.groups.index'));
            $this->assertEquals(1, substr_count($response->getContent(), 'Group'));
        }
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_details()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // 詳細を取得する
        $response = $this->get(route('admin.groups.show', $group->id));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_groups_details()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // グループ詳細を取得し、特定のグループが表示されていることを確認する
        $response = $this->get(route('admin.groups.show', $group->id));
        $response->assertSee(['Group', 'GroupRemark']);
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_groups_detailed_view()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // グループの詳細を取得し、viewが正しく表示されることを確認する
        $response = $this->get(route('admin.groups.show', $group->id));
        $response->assertViewIs('admin.groups.show');
    }

    /**
     * @test
     */
    public function test_users_groups_get_ok_unauthenticated_details()
    {
        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // ユーザーがゲスト状態（ログアウトの状態）で詳細を取得する
        $response = $this->get(route('admin.groups.show', $group->id));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.create'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で新規作成画面を取得する
        $response = $this->get(route('admin.groups.create'));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok_groups_create_view()
    {
        // 管理者としてログインし、viewが正しく表示されることを確認する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.create'));
        $response->assertViewIs('admin.groups.create');
    }

    /**
     * @test
     */
    public function test_admin_groups_post_ok_store()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // 作成されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks
        ]);
        $this->assertDatabaseHas('groups_courses', [
            'group_id' => $group->id,
            'course_id' => $course->id,
        ]);
        $this->assertDatabaseHas('users_groups', [
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function test_admin_groups_post_ok_redirect()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // 新規作成後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');
    }

    /**
     * @test
     */
    public function test_admin_groups_post_ok_message()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // 新規作成された際に適切なメッセージが表示されている確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');
        $response->assertSessionHas('message', $group->group_name . 'を登録しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // 編集画面を取得する
        $response = $this->get(route('admin.groups.edit', ['group' => $group->id]));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_unauthenticated()
    {
        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // ユーザーがゲスト状態（ログアウトの状態）で編集画面を取得する
        $response = $this->get(route('admin.groups.edit', ['group' => $group->id]));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_back_show()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // showの場合、特定のグループへの戻るボタンへのリンクを生成する
        $responseShow = $this->get(route('admin.groups.edit', ['group' => $group->id, 'show' => 'show']));
        $responseShow->assertViewHas('backBtn', route('admin.groups.show', ['group' => $group->id]));
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_back_indx()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

       // showでない場合、デフォルトの戻るボタンへのリンクを生成する
        $responseNotShow = $this->get(route('admin.groups.edit', ['group' => $group->id]));
        $responseNotShow->assertViewHas('backBtn', route('admin.groups.index'));
    }


    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_groups_edit_view()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $group->courses()->attach($course);
        $group->users()->attach($user);

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $group->group_name,
            'remarks' => $group->remarks,
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        // viewが正しく表示されることを確認する
        $response = $this->get(route('admin.groups.edit', ['group' => $group->id]));
        $response->assertViewIs('admin.groups.edit');
    }

    /**
     * @test
     */
    public function test_admin_groups_patch_ok_update()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $newUser = $this->createTestUsers();
        $newGroup = $this->createTestGroups();
        $newCourse = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $newGroup->courses()->attach($newCourse);
        $newGroup->users()->attach($newUser);

        // グループを編集
        $this->patch(route('admin.groups.update', ['group' => $newGroup->id]), [
            'group_name' => $newGroup->group_name,
            'remarks' => $newGroup->remarks,
            'user' => [$newUser->id],
            'course' => [$newCourse->id],
        ]);

        // 編集されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => $newGroup->group_name,
            'remarks' => $newGroup->remarks
        ]);
        $this->assertDatabaseHas('groups_courses', [
            'group_id' => $newGroup->id,
            'course_id' => $newCourse->id,
        ]);
        $this->assertDatabaseHas('users_groups', [
            'group_id' => $newGroup->id,
            'user_id' => $newUser->id,
        ]);
    }

    /**
     * @test
     */
    public function test_admin_groups_patch_ok_redirect()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $newUser = $this->createTestUsers();
        $newGroup = $this->createTestGroups();
        $newCourse = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $newGroup->courses()->attach($newCourse);
        $newGroup->users()->attach($newUser);

        // グループを編集
        $response = $this->patch(route('admin.groups.update', ['group' => $newGroup->id]), [
            'group_name' => $newGroup->group_name,
            'remarks' => $newGroup->remarks,
            'user' => [$newUser->id],
            'course' => [$newCourse->id],
        ]);

        // 編集後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');
    }

    /**
     * @test
     */
    public function test_admin_groups_patch_ok_message()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $newUser = $this->createTestUsers();
        $newGroup = $this->createTestGroups();
        $newCourse = $this->createTestCourses();

        // グループとコース、ユーザーを関連付ける
        $newGroup->courses()->attach($newCourse);
        $newGroup->users()->attach($newUser);

        // グループを編集
        $response = $this->patch(route('admin.groups.update', ['group' => $newGroup->id]), [
            'group_name' => $newGroup->group_name,
            'remarks' => $newGroup->remarks,
            'user' => [$newUser->id],
            'course' => [$newCourse->id],
        ]);

        // 編集された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', $newGroup->group_name . 'を編集しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_destroy_ok_delete()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // 削除を実行し、グループが論理削除されているか確認する。
        $group->delete();
        $this->assertSoftDeleted($group);
    }

    /**
     * @test
     */
    public function test_admin_groups_destroy_ok_redirect()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // グループの削除後に正しいリダイレクトが行われていることを確認する。
        $response = $this->delete(route('admin.groups.destroy', $group->id));
        $response->assertStatus(302)->assertRedirect('admin/groups');
    }

    /**
     * @test
     */
    public function test_admin_groups_destroy_ok_message()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループとユーザーを関連付ける
        $group = $this->createTestGroups();
        $this->createTestUsers()->groups()->attach($group);

        // グループとコースを関連付ける
        $course = $this->createTestCourses();
        $group->courses()->attach($course);

        // グループが削除された際に適切なメッセージが表示されている確認する。
        $response = $this->delete(route('admin.groups.destroy', $group->id));
        $response->assertSessionHas('danger', 'Group' . 'を削除しました');
    }
}