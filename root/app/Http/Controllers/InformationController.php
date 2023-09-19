<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateInformationRequest;
use App\Models\Information;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informations = Information::all();
        return view('admin.information.index', compact('informations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.information.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        Information::create([
            'title' => $request->title,
            'text'  => $request->text,
        ]);

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

        return redirect()->route('admin.information.index')->with('message', $request->title.'を更新しました');
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
