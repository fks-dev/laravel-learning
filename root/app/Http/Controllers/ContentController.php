<?php

namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Record;
use App\Http\Requests\StoreContentRequest;
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
        $adminUser = Auth::user();
        return view('admin.contents.index', compact('contents','course','courseTitle', 'adminUser'));
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

        if ($original->movie_file_path != null) {
            $info = pathinfo($original->movie_file_path);
            $parts = explode('_', $info['filename'], 2);
            $movieName = time() . '_' . end($parts) . '.' . $info['extension'];
            Storage::disk('public')->copy($original->movie_file_path, 'movies/' . $movieName);
            $newContent->update(['movie_file_path' => 'movies/' . $movieName]);
        } elseif ($original->document_file_path != null) {
            $info = pathinfo($original->document_file_path);
            $parts = explode('_', $info['filename'], 2);
            $fileName = time() . '_' . end($parts) . '.' . $info['extension'];
            Storage::disk('public')->copy($original->document_file_path, 'handout/' . $fileName);
            $newContent->update(['document_file_path' => 'handout/' . $fileName]);
        }

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
        return redirect()->route('admin.contents.index', compact('course'))
                         ->with('danger', $content->title . 'を削除しました');
    }

    public function list(Course $course){ //コンテンツ一覧画面
        $user = Auth::user();
        $course_title = $course->title;
        $contents = Content::where('course_id', $course->id)->get();

        return view('users.contents.index', compact('contents','user','course_title'));
    }
    public function view(Content $content){ //コンテンツ詳細画面
        $user = Auth::user();
        $title = $content->title;
        return view('users.contents.show', compact('content','user','title'));
    }

}
