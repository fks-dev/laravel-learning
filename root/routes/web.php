<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminMgmtController;
use App\Http\Controllers\UserMgmtController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\UserMessageController;
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

Route::prefix('admin')->name('admin')->controller(AdminLoginController::class)->group(function () {
    // 管理ログイン画面
    Route::get('', 'index')->name('');
    // 管理ログイン
    Route::post('', 'login')->name('.login');
    // 管理ログアウト
    Route::delete('', 'logout')->name('.logout');
});


// 管理ログイン後のみアクセス可
Route::middleware('auth:admin')->group(function () {
    Route::prefix('admin')->name('admin')->group(function () {
        // コース
        Route::prefix('courses')->name('.course')->controller(CourseController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::post('sort', 'sort')->name('.sort');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{course}/edit', 'edit')->name('.edit');
            Route::patch('{course}', 'update')->name('.update');
            Route::delete('{course}', 'destroy')->name('.destroy');
        });

        // コンテンツ
        Route::prefix('contents')->name('.content')->controller(ContentController::class)->group(function () {
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
        Route::prefix('admin-mgmt')->name('.adminMgmt')->controller(AdminMgmtController::class)->group(function() {
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
            Route::post('download-csv', 'downloadCsv')->name('.download-csv');
            // CSVインポート
            Route::get('create-csv', 'createCsv')->name('.create-csv');
            Route::post('store-csv', 'storeCsv')->name('.store-csv');
        });

        // ユーザー　一覧画面
        Route::prefix('user-mgmt')->name('.userMgmt')->controller(UserMgmtController::class)->group(function() {
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
            Route::post('download-csv', 'downloadCsv')->name('.download-csv');
            // CSVインポート
            Route::get('create-csv', 'createCsv')->name('.create-csv');
            Route::post('store-csv', 'storeCsv')->name('.store-csv');
        });
        Route::prefix('messages')->name('admin.message')->controller(AdminMessageController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('draft', 'draft')->name('.draft'); //下書き
            Route::get('sent', 'sent')->name('.sent'); //送信済み
            Route::get('dust', 'dust')->name('.dust'); //ゴミ箱
            Route::post('dust/{message}', 'restore')->name('.restore'); //復元
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{message}', 'show')->name('.show');
            Route::get('{message}/sent', 'sentShow')->name('.sent.show'); //送信済み
            Route::get('{message}/reply', 'reply')->name('.reply'); //返信メール
            Route::post('{message}', 'replyStore')->name('.reply.store'); //返信メール保存
            Route::get('{message}/edit', 'edit')->name('.edit');
            Route::patch('{message}', 'update')->name('.update');
            Route::delete('{message}', 'destroy')->name('.destroy');
            Route::post('{message}/hidden', 'hidden')->name('.hidden'); //非表示
        });
    });
});

// ユーザーログイン画面
Route::get('/users/login', [UserLoginController::class, 'index'])->name('users.login.index');
// ユーザーログイン
Route::post('/users/login', [UserLoginController::class, 'login'])->name('users.login');
// ユーザーログアウト
Route::delete('/users/login', [UserLoginController::class, 'logout'])->name('users.logout');

// ユーザーログイン後のみアクセス可
Route::middleware('auth:web')->group(function () {
    Route::get('users', function () {
        return view('users.index');
    })->name('users.index');

    Route::prefix('users')->group(function () {

        Route::prefix('messages')->name('user.message')->controller(UserMessageController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('draft', 'draft')->name('.draft'); //下書き
            Route::get('sent', 'sent')->name('.sent'); //送信済み
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{message}', 'show')->name('.show');
            Route::get('{message}/sent', 'sentShow')->name('.sent.show'); //送信済み
            Route::get('{message}/reply', 'reply')->name('.reply'); //返信メール
            Route::post('{message}', 'replyStore')->name('.reply.store'); //返信メール保存
            Route::get('{message}/edit', 'edit')->name('.edit');
            Route::patch('{message}', 'update')->name('.update');
            Route::delete('{message}', 'destroy')->name('.destroy');

            Route::post('{message}/hidden', 'hidden')->name('.hidden');
            Route::get('dustBox', 'dustBox')->name('.dustBox'); //削除済み
        });
    });
});
