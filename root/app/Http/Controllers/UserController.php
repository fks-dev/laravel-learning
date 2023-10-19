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

        return view('users.index', compact('user'));
    }
}
