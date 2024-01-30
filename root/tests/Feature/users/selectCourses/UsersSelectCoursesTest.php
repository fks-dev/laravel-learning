<?php

namespace Tests\Feature\Users\selectCourses;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;

class UsersSelectCoursesTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private const YES = 'yes';
    private const NO = 'no';
    private const QUESTION = [
        [
            'q_id' => 0,
            'q_order' => 1,
            'text' => "最初の質問です。0ですか？",
            self::YES => 1,
            self::NO => 2
        ], [
            'q_id' => 1,
            'q_order' => 2,
            'text' => "2問目です。1ですか？",
            self::YES => 3,
            self::NO => 4
        ], [
            'q_id' => 2,
            'q_order' => 2,
            'text' => "2問目です。2ですか？",
            self::YES => 5,
            self::NO => 6
        ], [
            'q_id' => 3,
            'q_order' => 3,
            'text' => "3問目です。3ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 4,
            'q_order' => 3,
            'text' => "3問目です。4ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 5,
            'q_order' => 3,
            'text' => "3問目です。5ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 6,
            'q_order' => 3,
            'text' => "3問目です。6ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ],
    ];

    /**
     * テスト用データを作成
     */
    public function setUp(): void
    {
        parent::setUp();

        //ユーザーを作成
        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('password1'),
            'mail_address' => 'testUser1@user.com',
        ]);

        //ログインする
        $this->actingAs($this->user);

        //診断結果用のコースを作成
        Course::factory()->create([
            'id' => 190001,
            'title' => 'test_course_yes',
        ]);

        Course::factory()->create([
            'id' => 190002,
            'title' => 'test_course_no',
        ]);
    }

    /**
     * @test
     * ユーザーがおすすめ動画診断画面に正常にアクセスできることを確認する
     */
    public function test_users_select_courses_get_ok()
    {
        $response = $this->get('/users/select-courses');
        $response->assertOk();

        //最初の質問が表示されているか確認
        $response->assertSee(self::QUESTION[0]['text']);
    }

    /**
     * @test
     * ユーザーが未ログイン時におすすめ動画診断画面にアクセスできないことを確認する
     */
    public function test_users_select_courses_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/users/select-courses');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/users/login');
    }

    /**
     * データプロバイダ: 1問目と2問目の質問に対するテストデータ
     */
    public function provideTestDataForFirstAndSecondQuestions()
    {
        return [
            [0, self::YES, self::QUESTION[1]['text']],
            [0, self::NO, self::QUESTION[2]['text']],
            [1, self::YES, self::QUESTION[3]['text']],
            [1, self::NO, self::QUESTION[4]['text']],
            [2, self::YES, self::QUESTION[5]['text']],
            [2, self::NO, self::QUESTION[6]['text']],
        ];
    }

    /**
     * @test
     * ユーザーが1問目または2問目の質問に対して回答をした場合、次の質問が正しく表示されていることを確認する
     * @dataProvider provideTestDataForFirstAndSecondQuestions
     */
    public function test_users_select_courses_get_ok_answer_question($q_id, $answer, $NextQuestionText)
    {
        $response = $this->get("/users/select-courses?q_id=$q_id&answer=$answer");

        //次の質問が表示されているか確認
        $response->assertSee($NextQuestionText);
    }

    /**
     * データプロバイダ: 3問目の質問に対するテストデータ
     */
    public function provideTestDataForThirdQuestion()
    {
        return [
            [3, self::YES, self::QUESTION[3]['yes_course_id']],
            [3, self::NO, self::QUESTION[3]['no_course_id']],
            [4, self::YES, self::QUESTION[4]['yes_course_id']],
            [4, self::NO, self::QUESTION[4]['no_course_id']],
            [5, self::YES, self::QUESTION[5]['yes_course_id']],
            [5, self::NO, self::QUESTION[5]['no_course_id']],
            [6, self::YES, self::QUESTION[6]['yes_course_id']],
            [6, self::NO, self::QUESTION[6]['no_course_id']],
        ];
    }

    /**
     * @test
     * ユーザーが3問目の質問に対して回答をした場合、回答に対応したコースが正しく表示されていることを確認する
     * @dataProvider provideTestDataForThirdQuestion
     */
    public function test_users_select_courses_get_ok_answer_third_question($q_id, $answer, $expectedCourseId)
    {
        $response = $this->get("/users/select-courses?q_id=$q_id&answer=$answer");

        //ビューが正常に表示されているか確認
        $response->assertViewIs('users.recommend.answer');

        //ビューに正しいコースが表示されているか確認
        $response->assertViewHas('course', Course::find($expectedCourseId));

        //期待されるテキストが表示されているか確認
        $expectedTitle = Course::find($expectedCourseId)['title'];
        $response->assertSee("あなたへのおすすめ動画は{$expectedTitle}です。");

        //コースのコンテンツへのリンクが正しく表示されているか確認
        $response->assertSee("/users/contents/$expectedCourseId");
    }
}
