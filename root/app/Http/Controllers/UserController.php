<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();

        return view('users.index', compact('user', 'courses'));
    }
}
