<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class SelectCourseController extends Controller
{
    public function index(Request $request)
    {
        if($request->input()){ //質問の回答入力を受け取っている場合
            $q_id = $request->input('q_id');
            $answer = $request->input('answer');
        }else{ //質問の回答入力を受け取っていない場合
            $q = $this->question[0]; //1問目の質問をviewに渡す
            return view('users.recommend.index', compact('q'));
        }

        //次の質問を取得する。次の質問がなければnullを代入する
        $next_q_id = $this->question[$q_id][$answer] ?? null;

        //次の質問が存在するなら$qとしてviewに渡す
        if($next_q_id){
            $q = $this->question[$next_q_id];
            return view('users.recommend.index', compact('q'));
        }

        if($answer=='yes'){
            $course = Course::find($this->question[$q_id]['yes_course_id']) ?? null;
        }
        if($answer=='no'){
            $course = Course::find($this->question[$q_id]['no_course_id']) ?? null;
        }
        if($course){ //回答に対応するCourseが正常に取得できていれば結果表示画面に$courseとして渡す
            return view('users.recommend.answer', compact('course'));
        }

        //まだreturnされていない場合はエラー

    }

    private $question = [
        # q_id 質問固有の番号
        # q_order 何問目の質問か
        # text 質問文
        # yes はいと回答した場合の次回の質問
        # no いいえと回答した場合の次回の質問
        [
            'q_id' => 0,
            'q_order' => 1,
            'text' => "最初の質問です。○○ですか？",
            'yes' => 1,
            'no' => 2
        ], [
            'q_id' => 1,
            'q_order' => 2,
            'text' => "2問目です。○○ですか？",
            'yes' => 3,
            'no' => 4
        ], [
            'q_id' => 2,
            'q_order' => 2,
            'text' => "2問目です。○○ですか？",
            'yes' => 5,
            'no' => 6
        ], [
            'q_id' => 3,
            'q_order' => 3,
            'text' => "3問目です。あなたは○○ですか？",
            'yes' => null,
            'no' => null,
            'yes_course_id' =>1,
            'no_course_id' =>2,
        ], [
            'q_id' => 4,
            'q_order' => 3,
            'text' => "3問目です。あなたは○○ですか？",
            'yes' => null,
            'no' => null,
            'yes_course_id' =>1,
            'no_course_id' =>2,
        ], [
            'q_id' => 5,
            'q_order' => 3,
            'text' => "3問目です。あなたは○○ですか？",
            'yes' => null,
            'no' => null,
            'yes_course_id' =>1,
            'no_course_id' =>2,
        ], [
            'q_id' => 6,
            'q_order' => 3,
            'text' => "3問目です。あなたは○○ですか？",
            'yes' => null,
            'no' => null,
            'yes_course_id' =>1,
            'no_course_id' =>2,
    ],
    ];
}
