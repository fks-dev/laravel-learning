<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        //ログイン中のユーザーに向けたお知らせを取得
        $groups = $user->groups ?? collect();
        $informations = collect();
        foreach($groups as $group){
            $informations = $informations->concat($group->informations);
        }
        //重複を除外し、最新の5件のみ取得する
        $informations = $informations->unique('id')->sortByDesc('updated_at')->take(5);

        return view('users.index', compact('user','informations'));
    }
}
