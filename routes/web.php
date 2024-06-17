<?php

use Illuminate\Support\Facades\Route;
// use app\Http\Controllers\LoginController;
// use app\Http\Controllers\Register;
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
    Route::group(["prefix"=>"/verify"],function(){
        Route::group(['prefix'=>'/create'],function(){
            Route::post('/password','Services\MailController@createForgotPassword');
            Route::post('/email','Services\MailController@createVerifyEmail');
        });
        Route::group(['prefix'=>'/password'],function(){
            Route::get('/{any?}','UserController@getChangePass')->where('any','.*');
            Route::post('/','UserController@changePassEmail');
        });
        Route::group(['prefix'=>'/email'],function(){
            Route::get('/{any?}','UserController@verifyEmail')->where('any','.*');
            Route::post('/','UserController@verifyEmail');
        });
        Route::group(['prefix'=>'/otp'],function(){
            Route::post('/password','UserController@getChangePass');
            Route::post('/email','UserController@verifyEmail');
        });
    });
});
?>