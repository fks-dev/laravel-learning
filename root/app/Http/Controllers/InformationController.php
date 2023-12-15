<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateInformationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Information;
use App\Models\Group;
use App\Models\User;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function getAdminId()
    {
        return Auth::guard('admin')->user()->id;
    }

    private function getCurrentUser(): User
    {
        return Auth::user();
    }

    public function index()
    {
        $admin_id = $this->getAdminId();
        $informations = Information::where('admin_id', $admin_id)->orderByDesc('updated_at')->get();
        $adminUser = $this->getCurrentUser();
        return view('admin.informations.index', compact('informations', 'adminUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::orderByDesc('id')->get();
        $adminUser = $this->getCurrentUser();
        return view('admin.informations.create', compact('groups', 'adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        $groups = $request->input('group', []);
        $admin_id = $this->getAdminId();
        $information = Information::create([
            'title' => $request->title,
            'text'  => $request->text,
            'admin_id' => $admin_id
        ]);
        $information->groups()->attach($groups);

        return redirect()->route('admin.informations.index')->with('message', 'お知らせを登録しました');
    }

    public function adminShow(Information $information)
    {
        $adminUser = $this->getCurrentUser();
        return view('admin.informations.show', compact('adminUser', 'information'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Information $information)
    {
        $groups = Group::orderByDesc('id')->get();
        $info_groups = $information->groups;
        $adminUser = $this->getCurrentUser();
        return view('admin.informations.edit', compact('information', 'groups', 'info_groups', 'adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformationRequest $request, Information $information)
    {
        $groups = $request->input('group', []);
        $information->groups()->sync($groups);

        $information->update([
            'title' => $request->title,
            'text'  => $request->text,
        ]);

        return redirect()->route('admin.informations.index')->with('message', $request->title . 'を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information $information)
    {
        $information->delete();
        return redirect()->route('admin.informations.index')->with('danger', $information->title . 'を削除しました');
    }

    public function list()
    {
 //ユーザーのお知らせ一覧画面
        $user = $this->getCurrentUser();
        $groups = $user->groups ?? collect();
        $informations = collect();
        foreach ($groups as $group) {
            $informations = $informations->concat($group->informations);
        }
        $informations = $informations->unique('id')->sortByDesc('updated_at');
        return view('users.informations.index', compact('informations', 'user'));
    }
    public function show(Information $information)
    {
        $user = $this->getCurrentUser();
        return view('users.informations.show', compact('information', 'user'));
    }
}
