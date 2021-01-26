<?php

use Illuminate\Support\Facades\{Route, Auth};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->name('egg.')->group(function () {
    // 首页
    Route::get('/', 'StockController@index')->name('home');
    // 基金的CURD
    Route::resource('userStock', 'StockController')->except('show', 'create', 'edit');
    // 搜索基金
    Route::get('search', 'StockController@search')->name('search');
    // 上传图片更新基金
    Route::post('updateByImg', 'StockController@uploadImg')->name('updateByImg');
});

// 登录
// Auth::routes();
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login')->name('doLogin');
