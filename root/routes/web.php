<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CourseController;
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
});
