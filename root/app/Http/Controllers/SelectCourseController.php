<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelectCourseController extends Controller
{
    public function index(){
        return view('users.recommend.index');
    }
}
