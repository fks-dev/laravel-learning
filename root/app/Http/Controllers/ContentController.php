<?php

namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\Admin;
use App\Models\Course;
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
        return view('admin.contents.index', compact('contents','course','courseTitle'));
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
        return view('admin.contents.create', compact('course', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContentRequest $request, $course)
    {
        $kind = $request->input('kind');
        $user = Auth::guard('admin')->user();

        if ($kind == 'ラベル') { //ラベル
            Content::create([
                'course_id'=> $request->course_id,
                'admin_id' => $user->id,
                'title'    => $request->title,
                'kind'     => $kind,
                'public'   => $request->public,
            ]);
        } else {
            $data = [
                'course_id'=> $request->course_id,
                'admin_id' => $user->id,
                'title'   => $request->title,
                'kind'    => $kind,
                'remarks' => $request->remarks,
                'public'  => $request->public,
            ];
            if ($kind == 'リッチテキスト') { //リッチテキスト
                $data['text'] = $request->text;

            } elseif ($kind == '動画') { //動画ファイル
                $movie         = $request->file('movie');
                $movieName     = time() . '_' . $movie->getClientOriginalName();
                $moviePath     = $movie->storeAs('movies', $movieName, 'public');
                $data['movie'] = $moviePath;

            } elseif ($kind == 'URL') { //URL埋め込み
                $url         = $request->input('url');
                $data['url'] = $url;

            }elseif ($kind == '資料') { //配布資料
                $file         = $request->file('file');
                $fileName     = time() . '_' . $file->getClientOriginalName();
                $filePath     = $file->storeAs('handout', $fileName, 'public');
                $data['file'] = $filePath;

            } elseif ($kind == 'テスト') {
                $data['testTime'] = $request->testTime;
                $data['testPer']  = $request->testPer;
                $data['testVol']  = $request->testVol;
            }
            Content::create($data);
        }

        return redirect()->route('content.index', compact('course'))->with('message', 'コンテンツを登録しました');
    }

    /**
     * show
     */
    public function show(Content $content)
    {
        $admin = Admin::where('id', $content->admin_id)->first();

        return view('admin.contents.show', compact('content', 'admin'));
    }

    /**
     * ファイルダウンロード
     */
    public function download(Content $content)
    {
        return Storage::download('public/' . $content->file);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        $courses = Course::all();
        return view('admin.contents.edit', compact('content', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContentRequest $request, Content $content)
    {
        $course = $content->course_id;
        $kind = $request->input('kind');

        if ($kind == 'ラベル') { //ラベル
            $content->update([
                'course_id'=> $request->course_id,
                'admin_id' => Auth()->user()->id,
                'title'    => $request->title,
                'kind'     => $kind,
                'public'   => $request->public,
            ]);
        } else {
            $data = [
                'course_id'=> $request->course_id,
                'admin_id' => Auth()->user()->id,
                'title'   => $request->title,
                'kind'    => $kind,
                'remarks' => $request->remarks,
                'public'  => $request->public,
            ];
            if ($kind == 'リッチテキスト') { //リッチテキスト
                $data['text'] = $request->text;

            } elseif ($kind == '動画' && $request->movie != null) {  //動画ファイル
                // 古い動画を削除
                Storage::disk('public')->delete($content->movie);
                // 動画追加
                $movie         = $request->file('movie');
                $movieName     = time() . '_' . $movie->getClientOriginalName();
                $moviePath     = $movie->storeAs('movies', $movieName, 'public');
                $data['movie'] = $moviePath;

            } elseif ($kind == 'URL') { //URL埋め込み
                $url         = $request->input('url');
                $data['url'] = $url;

            }elseif ($kind == '資料' && $request->file != null) { //配布資料
                // 古い資料を削除
                Storage::disk('public')->delete($content->file);
                // 配布資料追加
                $file         = $request->file('file');
                $fileName     =  time() . '_' . $file->getClientOriginalName();
                $filePath     = $file->storeAs('handout', $fileName, 'public');
                $data['file'] = $filePath;

            } elseif ($kind == 'テスト') { //テスト
                $data['testTime'] = $request->testTime;
                $data['testPer']  = $request->testPer;
                $data['testVol']  = $request->testVol;
            }
            $content->update($data);
        }

        return redirect()->route('content.index', compact('course'))->with('message', 'コンテンツを変更しました');
    }

    /**
     * 複製
     */
    public function duplicate($content)
    {
        $original = Content::findOrFail($content);
        $newContent = new Content();
        $newContent->fill($original->toArray())->save();

        if ($original->movie != null) {
            $info = pathinfo($original->movie);
            $parts = explode('_', $info['filename'], 2);
            $movieName = time() . '_' . end($parts) . '.' . $info['extension'];
            Storage::disk('public')->copy($original->movie, 'movies/' . $movieName);
            $newContent->update(['movie' => 'movies/' . $movieName]);
        } elseif ($original->file != null) {
            $info = pathinfo($original->file);
            $parts = explode('_', $info['filename'], 2);
            $fileName = time() . '_' . end($parts) . '.' . $info['extension'];
            Storage::disk('public')->copy($original->file, 'handout/' . $fileName);
            $newContent->update(['file' => 'handout/' . $fileName]);
        }

        $course = $newContent->course_id;

        return redirect()->route('content.index', compact('course'))->with('message', 'コンテンツを複製しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $course = $content->course_id;
        $content->delete();
        return redirect()->route('content.index', compact('course'))->with('danger', $content->title . 'を削除しました');
    }
}
