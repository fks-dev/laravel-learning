<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::all();
        $loggedInAdmin = Auth::user();
        return view('admin.groups.index', compact('groups', 'loggedInAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        $users = User::all();
        $loggedInAdmin = Auth::user();
        return view('admin.groups.create', compact('courses', 'users', 'loggedInAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        Group::create([
            'group_name' => $request->group_name,
            'remarks'    => $request->remarks,
        ]);

        $group = Group::orderByDesc('id')->first();
        $courses = $request->input('course', []);
        $users = $request->input('user', []);

        $group->courses()->attach(Course::findMany($courses));
        $group->users()->attach(User::findMany($users));

        return redirect()->route('admin.group.index')->with('message', $request->group_name.'を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $loggedInAdmin = Auth::user();
        return view('admin.groups.show', compact('group', 'loggedInAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Request $request)
    {
        $courses = Course::all();
        $users = User::all();
        $loggedInAdmin = Auth::user();
        $show = $request->input('show');

        if ($show === 'show') {
            $backBtn = route('admin.group.show', $group);
        }else {
            $backBtn = route('admin.group.index');
        }

        return view('admin.groups.edit', compact('courses', 'users', 'loggedInAdmin', 'group', 'backBtn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update([
            'group_name' => $request->group_name,
            'remarks'    => $request->remarks,
        ]);

        $courses = $request->input('course', []);
        $users = $request->input('user', []);

        $group->courses()->sync($courses);
        $group->users()->sync($users);

        return redirect()->route('admin.group.index')->with('message', $request->group_name.'を編集しました');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('admin.group.index')->with('danger', $group->group_name . 'を削除しました');
    }
}
