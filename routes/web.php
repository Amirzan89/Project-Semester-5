<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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
    Route::get('/login',function(Request $request){
        if($request->wantsJson()){
            return response()->json(['status' => 'success', 'message' => 'OK']);
        }
        return view('login');
    });
    Route::get('/register',function(Request $request){
        if($request->wantsJson()){
            return response()->json(['status' => 'success', 'message' => 'OK']);
        }
        return view('register');
    });
    Route::get('/dashboard',function(){
        return view('dashboard');
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
    Route::group(["prefix"=>"/password"],function(){
        Route::get('/reset',function(Request $request){
            if($request->wantsJson()){
                return response()->json(['status' => 'success', 'message' => 'OK']);
            }
            return view();
        });
    });
    Route::group(["prefix"=>"/device"],function(){
        Route::get('/', 'Page/DeviceController@showDevice');
        Route::get('/{any}', 'Page/DeviceController@detailDevice');

        Route::post('/create', 'Services/DeviceController@createDevice');
        Route::post('/register', 'Services/DeviceController@registerDevice');
        Route::put('/{id}', 'Services/DeviceController@updateDevice');
        Route::delete('/{id}', 'Services/DeviceController@deleteDevice');
    });
    Route::group(["prefix"=>"/firmware"],function(){
        Route::get('/', 'Page/FirmwareController@showFirmware');
        Route::get('/{id}', 'Page/FirmwareController@detailFirmware');

        Route::post('/', 'Services/FirmwareController@createFirmware');
        Route::put('/update', 'Services/FirmwareController@updateFirmware');
        Route::delete('/', 'Services/FirmwareController@deleteFirmware');
    });
    // Route::group(["prefix"=>"/laporan"],function(){
    //     Route::get('/', 'Page/LaporanController@');
    //     Route::get('/{id}', 'Page/LaporanController@');

    //     Route::post('/register', 'Services/LaporanController@');
    //     Route::post('/activate', 'Services/LaporanController@');
    //     Route::put('/update', 'Services/LaporanController@');
    //     Route::delete('/delete', 'Services/LaporanController@');
    // });
    Route::get('/profile','Page\HomeController@profile')->where('any','.*');
    Route::get('/dashboard','Page\HomeController@dashboard')->where('any','.*');
    Route::get('/','Page\HomeController@getHome')->where('any','.*');
});
?>