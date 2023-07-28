<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\UserLoginController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 管理ログイン画面
Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
// 管理ログイン
Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');
// 管理ログアウト
Route::delete('/admin/login', [AdminLoginController::class, 'destroy'])->name('admin.login.destroy');

// 管理ログイン後のみアクセス可
Route::middleware('auth:admin')->group(function () {
    Route::prefix('admin')->name('admin')->controller(AdminController::class)->group(function() {
        Route::get('', 'index')->name('.index');
        Route::post('search', 'search')->name('.search');
        Route::post('sort', 'sort')->name('.sort');
        Route::get('create', 'create')->name('.create');
        Route::post('', 'store')->name('.store');
        Route::get('{admin}/edit', 'edit')->name('.edit');
        Route::patch('{admin}', 'update')->name('.update');
        Route::delete('{admin}', 'destroy')->name('.destroy');
        // CSVエクスポート
        Route::post('csv', 'csv')->name('.csv');
        // CSVインポート
        Route::get('import', 'import')->name('.import');
        Route::post('import', 'importStore')->name('.import.store');
    });
});

// 管理ログイン画面
Route::get('/users/login', [UserLoginController::class, 'create'])->name('users.login');
// 管理ログイン
Route::post('/users/login', [UserLoginController::class, 'store'])->name('users.login.store');
// 管理ログアウト
Route::delete('/users/login', [UserLoginController::class, 'destroy'])->name('users.login.destroy');

// 管理ログイン後のみアクセス可
Route::middleware('auth:web')->group(function () {
    Route::get('users', function () {
        return view('users.index');
    })->name('users.index');
});
