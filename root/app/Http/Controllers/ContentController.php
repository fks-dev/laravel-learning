<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\ContentsLog;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\StoreContentLogRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($course)
    {
        $courseTitle = Course::select('title')->where('id', $course)->get();
        $contents = Content::where('course_id', $course)->orderby('position')->get();
<<<<<<< HEAD
        return view('admin.contents.index', compact('contents', 'course', 'courseTitle'));
=======
        $adminUser = Auth::user();
        return view('admin.contents.index', compact('contents','course','courseTitle', 'adminUser'));
>>>>>>> daf4ca98db80952a9c76be5f293fcc6cc194910c
    }

    /**
     * 並び替え
     */
    public function sort(Request $request)
    {
        $positions = $request->input('positions');

        DB::transaction(function () use ($positions) {
            foreach ($positions as $index => $id) {
                Content::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => '並び替えを保存しました。']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($course)
    {
        $courses = Course::all();
        $adminUser = Auth::user();
        return view('admin.contents.create', compact('course', 'courses', 'adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContentRequest $request, $course)
    {
        $user = Auth::user();

        $data = [
            'course_id'        => $request->course_id,
            'admin_id'         => $user->id,
            'title'            => $request->title,
            'youtube_video_id' => $request->youtube_video_id,
            'remarks'          => $request->remarks,
            'is_public'        => $request->is_public,
        ];

<<<<<<< HEAD
        if ($content_type != 1) {
            $data['remarks'] = $request->remarks;

            if ($content_type == 4) { //リッチテキスト
                $data['text'] = $request->text;
            } elseif ($content_type == 5) { //動画ファイル
                $movie         = $request->file('movie_file_path');
                $movieName     = time() . '_' . $movie->getClientOriginalName();
                $moviePath     = $movie->storeAs('movies', $movieName, 'public');
                $data['movie_file_path'] = $moviePath;
            } elseif ($content_type == 2) { //URL埋め込み
                $data['youtube_video_id'] = $request->youtube_video_id;
            } elseif ($content_type == 3) { //配布資料
                $file         = $request->file('document_file_path');
                $fileName     = time() . '_' . $file->getClientOriginalName();
                $filePath     = $file->storeAs('handout', $fileName, 'public');
                $data['document_file_path'] = $filePath;
            } elseif ($content_type == 6) {
                $data['time_limit_minutes'] = $request->time_limit_minutes;
                $data['passing_score_rate'] = $request->passing_score_rate;
                $data['amount_questions']   = $request->amount_questions;
            }
        }

=======
>>>>>>> 348cbd84cbc4ca3874f852b9c59cc526f7ba97f8
        Content::create($data);

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを登録しました');
    }

    /**
     * show
     */
    public function show(Content $content)
    {
        $admin = $content->admin;
        $adminUser = Auth::user();

        return view('admin.contents.show', compact('content', 'admin', 'adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        $courses = Course::all();
        $adminUser = Auth::user();
        return view('admin.contents.edit', compact('content', 'courses', 'adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContentRequest $request, Content $content)
    {
        $course = $content->course_id;

        $data = [
            'course_id'          => $request->course_id,
            'admin_id'           => Auth()->user()->id,
            'title'              => $request->title,
            'youtube_video_id'   => $request->youtube_video_id,
            'remarks'            => $request->remarks,
            'is_public'          => $request->is_public,
        ];

<<<<<<< HEAD
        if ($content_type != 1) {
            $data['remarks'] = $request->remarks;

            if ($content_type == 4) { //リッチテキスト
                $data['text'] = $request->text;
            } elseif ($content_type == 5 && $request->movie_file_path != null) {  //動画ファイル
                // 動画追加
                $movie         = $request->file('movie_file_path');
                $movieName     = time() . '_' . $movie->getClientOriginalName();
                $moviePath     = $movie->storeAs('movies', $movieName, 'public');
                $data['movie_file_path'] = $moviePath;
            } elseif ($content_type == 2) { //URL埋め込み
                $data['youtube_video_id'] = $request->youtube_video_id;
            } elseif ($content_type == 3 && $request->document_file_path != null) { //配布資料
                // 配布資料追加
                $file         = $request->file('document_file_path');
                $fileName     =  time() . '_' . $file->getClientOriginalName();
                $filePath     = $file->storeAs('handout', $fileName, 'public');
                $data['document_file_path'] = $filePath;
            } elseif ($content_type == 6) { //テスト
                $data['time_limit_minutes'] = $request->time_limit_minutes;
                $data['passing_score_rate'] = $request->passing_score_rate;
                $data['amount_questions']   = $request->amount_questions;
            }
        }

=======
>>>>>>> 348cbd84cbc4ca3874f852b9c59cc526f7ba97f8
        $content->update($data);

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを変更しました');
    }

    /**
     * 複製
     */
    public function duplicate($content)
    {
        $original = Content::findOrFail($content);
        $newContent = new Content();
        $newContent->fill($original->toArray())->save();

        $course = $newContent->course_id;

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを複製しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $course = $content->course_id;
        $content->delete();
<<<<<<< HEAD
        return redirect()->route('admin.content.index', compact('course'))
            ->with('danger', $content->title . 'を削除しました');
=======
        return redirect()->route('admin.contents.index', compact('course'))
                         ->with('danger', $content->title . 'を削除しました');
>>>>>>> daf4ca98db80952a9c76be5f293fcc6cc194910c
    }

    public function list(Course $course)
    { //コンテンツ一覧画面
        $user = Auth::user();
        $course_title = $course->title;
        $contents = Content::where('course_id', $course->id)->get();

        return view('users.contents.index', compact('contents', 'user', 'course_title'));
    }
    public function view(Content $content)
    { //コンテンツ詳細画面
        $user = Auth::user();
        $title = $content->title;
        return view('users.contents.show', compact('content', 'user', 'title'));
    }

    public function record(StoreContentLogRequest $request, Content $content)
    {
        $user = Auth::user();
        $completed = $request->input('log');
        $checkExists = ContentsLog::where('user_id',$user->id)->where('content_id',$content->id)->exists();
        if($checkExists){
            $contentsLog = ContentsLog::where('user_id',$user->id)->where('content_id',$content->id)->first();
            $contentsLog->update([
                'completed'=>$completed,
                'updated_at'=>now()
            ]);
            $contentsLog->touch();
        }else{
            ContentsLog::create([
                'user_id' => $user->id,
                'content_id' => $content->id,
                'completed' => $completed
            ]);
        }

        $course = $content->course;
        return to_route('users.content.index',$course);
    }
}
