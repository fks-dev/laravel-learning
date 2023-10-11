<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateInformationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Information;
use App\Models\Group;


class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function getAdminId()
    {
        return Auth::guard('admin')->user()->id;
    }

    public function index()
    {
        $admin_id = $this->getAdminId();
        $informations = Information::where('admin_id', $admin_id)->orderByDesc('updated_at')->get();
        return view('admin.information.index', compact('informations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::orderByDesc('id')->get();
        return view('admin.information.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        $groups = $request->input('group', []);
        $admin_id = $this->getAdminId();
        Information::create([
            'title' => $request->title,
            'text'  => $request->text,
            'admin_id' => $admin_id
        ]);
        $information = Information::orderByDesc('id')->first();
        $information->groups()->attach(Group::findMany($groups));

        return redirect()->route('admin.information.index')->with('message', 'お知らせを登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Information $information)
    {
        return view('admin.information.edit', compact('information'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformationRequest $request, Information $information)
    {
        $information->update([
            'title' => $request->title,
            'text'  => $request->text,
        ]);

        return redirect()->route('admin.information.index')->with('message', $request->title . 'を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information $information)
    {
        $information->delete();
        return redirect()->route('admin.information.index')->with('danger', $information->title . 'を削除しました');
    }
}
