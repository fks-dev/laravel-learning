<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\UserLogin;

class UserLoginController extends Controller
{
    /**
     * ログイン画面
     */
    public function index(): View
    {
        return view('users.login');
    }

    /**
     * ログイン
     */
    public function login(UserLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            // 過去ログを確認
            $loginLog = UserLogin::where('user_id', $user->id)->first();

            if ($loginLog) {
                $loginLog->updated_at = now();
                $loginLog->save();
            } else {
                $newLoginLog = new UserLogin();
                $newLoginLog->user_id = $user->id;
                $newLoginLog->updated_at = now();
                $newLoginLog->save();
            }
        }
        return redirect()->intended(route('users.index'));
    }

    /**
     * ログアウト
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('users.login.index');
    }
}
