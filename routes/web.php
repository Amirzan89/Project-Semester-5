<?php

use Illuminate\Support\Facades\Route;
use app\Http\Controllers\LoginController;
use app\Http\Controllers\Register;
/*
|--------------------------------------------------------------------------
| Web Routes2
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware'=>'auth'],function(){
    Route::get('/login',function(){
        return view('login');
    });
    Route::get('/register',function(){
        return view('register');
    });
    Route::get('/dashboard',function(){
        return view('dashboard');
    });
    Route::get('/csrf-token', function() {
        return response()->json(['token' => csrf_token()]);
    });
    Route::group(["prefix"=>"/users"],function(){
        Route::post('/login','Auth\LoginController@login');
        Route::post('/register','Auth\RegisterController@register');
        Route::post('/logout','UserController@logout');
        Route::get('/pengaturan',function(){
            return view('pengaturan');
        });
    });
    Route::group(["prefix"=>"/api"],function(){
        Route::post("/logout",function(){
            // return view('dashboard');
        });
        Route::post("/weather","TestController@weather");
    });
});
?>