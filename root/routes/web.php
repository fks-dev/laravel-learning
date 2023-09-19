<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserMgmtController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\ContentController;
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

Route::prefix('admin')->name('admin.login')->controller(AdminLoginController::class)->group(function () {
    // 管理ログイン画面
    Route::get('', 'create')->name('');
    // 管理ログイン
    Route::post('', 'store')->name('.store');
    // 管理ログアウト
    Route::delete('', 'destroy')->name('.destroy');
});


// 管理ログイン後のみアクセス可
Route::middleware('auth:admin')->group(function () {
    // コース画面
    Route::prefix('admin')->group(function () {

        Route::prefix('courses')->name('course')->controller(CourseController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::post('sort', 'sort')->name('.sort');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{course}/edit', 'edit')->name('.edit');
            Route::patch('{course}', 'update')->name('.update');
            Route::delete('{course}', 'destroy')->name('.destroy');
        });

        // コンテンツ
        Route::prefix('contents')->name('content')->controller(ContentController::class)->group(function () {
            Route::get('index/{course}', 'index')->name('.index');
            Route::post('sort', 'sort')->name('.sort'); //並べ替え
            Route::get('create/{course}', 'create')->name('.create');
            Route::post('index/{course}', 'store')->name('.store');
            Route::get('{content}', 'show')->name('.show');
            Route::get('{content}/download', 'download')->name('.download'); //ダウンロード
            Route::get('{content}/edit', 'edit')->name('.edit');
            Route::patch('{content}', 'update')->name('.update');
            Route::post('{content}/duplicate', 'duplicate')->name('.duplicate'); //複製
            Route::delete('{content}', 'destroy')->name('.destroy');
        });
        // 管理者一覧画面
        Route::prefix('adminMgmt')->name('adminMgmt')->controller(AdminController::class)->group(function() {
            Route::get('', 'index')->name('.index');
            Route::post('search', 'search')->name('.search');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{admin}/edit', 'edit')->name('.edit');
            Route::get('{admin}/password', 'password')->name('.password');
            Route::post('{admin}/password', 'changeAdminPassword')->name('.changePassword');
            Route::patch('{admin}', 'update')->name('.update');
            Route::delete('{admin}', 'destroy')->name('.destroy');
            // CSVエクスポート
            Route::post('csv', 'csv')->name('.csv');
            // CSVインポート
            Route::get('import', 'createCsv')->name('.import');
            Route::post('import', 'storeCsv')->name('.import.store');
        });

        // ユーザー　一覧画面
        Route::prefix('userMgmt')->name('userMgmt')->controller(UserMgmtController::class)->group(function() {
            Route::get('', 'index')->name('.index');
            Route::post('search', 'search')->name('.search');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{user}/edit', 'edit')->name('.edit');
            Route::get('{user}/password', 'password')->name('.password');
            Route::post('{user}/password', 'changeUserPassword')->name('.changePassword');
            Route::patch('{user}', 'update')->name('.update');
            Route::delete('{user}', 'destroy')->name('.destroy');
            // CSVエクスポート
            Route::post('csv', 'csv')->name('.csv');
            // CSVインポート
            Route::get('import', 'createCsv')->name('.import');
            Route::post('import', 'storeCsv')->name('.import.store');
        });
    });
});

// ユーザーログイン画面
Route::get('/users/login', [UserLoginController::class, 'create'])->name('users.login');
// ユーザーログイン
Route::post('/users/login', [UserLoginController::class, 'store'])->name('users.login.store');
// ユーザーログアウト
Route::delete('/users/login', [UserLoginController::class, 'destroy'])->name('users.login.destroy');

// ユーザーログイン後のみアクセス可
Route::middleware('auth:web')->group(function () {
    Route::get('users', function () {
        return view('users.index');
    })->name('users.index');
});
