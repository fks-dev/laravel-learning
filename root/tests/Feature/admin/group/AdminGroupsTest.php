<?php

namespace Tests\Feature\Admin\Group;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    // グループとの関連付け
    private function createGroupWithRelations(): array
    {
        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        $group->courses()->attach($course);
        $group->users()->attach($user);

        return compact('user', 'group', 'course');
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok()
    {
        // 管理者としてログインし、一覧画面を取得する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.index'));
        $response->assertStatus(200)->assertViewIs('admin.groups.index');
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
    public function test_admin_groups_get_ok_get_sort_created()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // 3つのグループを作成し、作成日時を設定する
        for ($i = 1; $i <= 3; $i++)
        {
            Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'created_at' => now()->subDays($i),
            ]);
        }

        // グループ一覧を取得し、作成日時の順にグループが並んでいるか確認する
        $response = $this->get(route('admin.groups.index'));
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_get_sort_updated()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // 3つのグループを作成し、更新日時を設定する
        for ($i = 1; $i <= 3; $i++)
        {
            Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'updated_at' => now()->subDays($i),
            ]);
        }

        // グループ一覧を取得し、更新日時の順にグループが並んでいるか確認する
        $response = $this->get(route('admin.groups.index'));
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_details()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // 詳細を取得する
        $response = $this->get(route('admin.groups.show', $relations['group']->id));
        $response->assertStatus(200)->assertViewIs('admin.groups.show');
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_groups_details()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // グループ詳細を取得し、特定のグループが表示されていることを確認する
        $response = $this->get(route('admin.groups.show', $relations['group']->id));
        $response->assertSee(['Group', 'GroupRemark']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_unauthenticated_details()
    {
        $relations = $this->createGroupWithRelations();

        // ユーザーがゲスト状態（ログアウトの状態）で詳細を取得する
        $response = $this->get(route('admin.groups.show', $relations['group']->id));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok()
    {
        // 管理者としてログインし、新規作成画面を取得する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.groups.create'));
        $response->assertStatus(200)->assertViewIs('admin.groups.create');
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
    public function test_admin_groups_post_ok_store()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $relations['group']->group_name,
            'remarks' => $relations['group']->remarks,
            'user' => [$relations['user']->id],
            'course' => [$relations['course']->id],
        ]);

        // 作成されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => $relations['group']->group_name,
            'remarks' => $relations['group']->remarks
        ]);
        $this->assertDatabaseHas('groups_courses', [
            'group_id' => $relations['group']->id,
            'course_id' => $relations['course']->id,
        ]);
        $this->assertDatabaseHas('users_groups', [
            'group_id' => $relations['group']->id,
            'user_id' => $relations['user']->id,
        ]);

        // 新規作成後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 新規作成された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', $relations['group']->group_name . 'を登録しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $relations['group']->group_name,
            'remarks' => $relations['group']->remarks,
            'user' => [$relations['user']->id],
            'course' => [$relations['course']->id],
        ]);

        // 編集画面を取得する
        $response = $this->get(route('admin.groups.edit', ['group' => $relations['group']->id]));
        $response->assertStatus(200)->assertViewIs('admin.groups.edit');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_unauthenticated()
    {
        $relations = $this->createGroupWithRelations();

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $relations['group']->group_name,
            'remarks' => $relations['group']->remarks,
            'user' => [$relations['user']->id],
            'course' => [$relations['course']->id],
        ]);

        // ユーザーがゲスト状態（ログアウトの状態）で編集画面を取得する
        $response = $this->get(route('admin.groups.edit', ['group' => $relations['group']->id]));

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect(route('admin.login.index'));
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_back_links()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // グループを新規作成
        $response = $this->post(route('admin.groups.store'), [
            'group_name' => $relations['group']->group_name,
            'remarks' => $relations['group']->remarks,
            'user' => [$relations['user']->id],
            'course' => [$relations['course']->id],
        ]);

        // showの場合、特定のグループの戻るボタンのリンクを生成する
        // showでない場合、デフォルトの戻るボタンのリンクを生成する
        $show = 'show';

        if ($show === 'show') {
            $response = $this->get(route('admin.groups.edit', ['group' => $relations['group']->id, 'show' => $show]));
            $response->assertViewHas('backBtn', route('admin.groups.show', ['group' => $relations['group']->id]));
        } else {
            $response = $this->get(route('admin.groups.edit', ['group' => $relations['group']->id]));
            $response->assertViewHas('backBtn', route('admin.groups.index'));
        }
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
        $response = $this->patch(route('admin.groups.update', ['group' => $newGroup->id]), [
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

        // 編集後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 編集された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', $newGroup->group_name . 'を編集しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_delete_ok_destroy()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // 正しいリダイレクトが行われていることを確認
        $response = $this->delete(route('admin.groups.destroy', $relations['group']->id));
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 削除を実行し、グループが論理削除されているか確認
        $relations['group']->delete();
        $this->assertSoftDeleted($relations['group']);

        // deleted_at カラムが適切に設定されていることを確認
        $this->assertNotNull($relations['group']->fresh()->deleted_at);

        // 削除メッセージがセッションに存在することを確認
        $this->assertNotNull(session('danger'));
    }
}