<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\UserLog;

class UserLoginController extends Controller
{
    /**
     * ログイン画面
     */
    public function create(): View
    {
        return view('users.login');
    }

    /**
     * ログイン
     */
    public function store(UserLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            // 過去ログを確認
            $loginLog = UserLog::where('user_id', $user->id)->first();

            if ($loginLog) {
                $loginLog->updated_at = now();
                $loginLog->save();
            } else {
                $newLoginLog = new UserLog();
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
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('users.login');
    }
}
