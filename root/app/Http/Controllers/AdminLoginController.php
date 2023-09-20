<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AdminLogin;

class AdminLoginController extends Controller
{
    /**
     * ログイン画面
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * ログイン
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();

            // 過去ログを確認
            $loginLog = AdminLogin::where('admin_id', $user->id)->first();

            if ($loginLog) {
                $loginLog->updated_at = now();
                $loginLog->save();
            } else {
                $newLoginLog = new AdminLogin();
                $newLoginLog->admin_id = $user->id;
                $newLoginLog->updated_at = now();
                $newLoginLog->save();
            }
        }
        return redirect()->intended(route('admin.adminMgmt.index'));

    }

    /**
     * ログアウト
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('admin.login');
    }
}
