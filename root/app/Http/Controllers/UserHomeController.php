<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserHomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        //ログイン中のユーザーに向けたお知らせを取得
        $groups = $user->groups ?? collect();
        $informations = collect();
        foreach ($groups as $group) {
            $informations = $informations->concat($group->informations);
        }
        //重複を除外し、最新の5件のみ取得する
        $informations = $informations->unique('id')->sortByDesc('updated_at')->take(5);

        //ログイン中のユーザーに向けたコースを取得
        $courses = collect();
        foreach ($groups as $group) {
            $courses = $courses->concat($group->courses);
        }
        $courses = $courses->unique('id')->sortBy('id');
        return view('users.index', compact('user', 'informations', 'courses'));
    }
}
