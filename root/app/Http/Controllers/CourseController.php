<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::orderby('position')->get();
        $loggedInAdmin = Auth::user();

        return view('admin.courses.index', compact('courses', 'loggedInAdmin'));
    }

    public function sort(Request $request)
    {
        $positions = $request->input('positions');

        DB::transaction(function () use ($positions) {
            foreach ($positions as $index => $id) {
                Course::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => '並び替えを保存しました。']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $loggedInAdmin = Auth::user();
        return view('admin.courses.create', compact('loggedInAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {

        Course::create([
            'title'       => $request->title,
            'introduction' => $request->introduction,
            'remarks'      => $request->remarks,
        ]);

        return redirect()->route('admin.course.index')->with('message', 'コースを登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $loggedInAdmin = Auth::user();
        return view('admin.courses.edit', compact('course', 'loggedInAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update([
            'title'       => $request->title,
            'introduction' => $request->introduction,
            'remarks'      => $request->remarks,
        ]);

        return redirect()->route('admin.course.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.course.index')->with('danger', $course->title . 'を削除しました');
    }
}
