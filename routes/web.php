<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
function getView($name = null){
    $env = env('APP_VIEW', 'blade');
    if($env == 'blade'){
        return view($name);
    }else if($env == 'inertia'){
        return inertia($name);
    }else if($env == 'nuxt'){
        $indexPath = public_path('dist/index.html');
        if (File::exists($indexPath)) {
            $htmlContent = File::get($indexPath);
            $htmlContent = str_replace('<body>', '<body>' . '<script>const csrfToken = "' . csrf_token() . '";</script>', $htmlContent);
            return response($htmlContent)->cookie('XSRF-TOKEN', csrf_token(), 0, '/', null, false, true);
        } else {
            return response()->json(['error' => 'Page not found'], 404);
        }
    }
}
Route::group(['middleware'=>'auth'],function(){
    //API for anyone or public
    Route::get('/','Page\HomeController@getHome')->where('any','.*');
    Route::get('/login',function(Request $request){
        if($request->wantsJson()){
            return response()->json(['status' => 'success', 'message' => 'OK']);
        }
        return getView('login');
    });
    Route::get('/register',function(Request $request){
        if($request->wantsJson()){
            return response()->json(['status' => 'success', 'message' => 'OK']);
        }
        return getView('register');
    });
    Route::get('/auth/redirect', 'Auth\LoginController@redirectToProvider');
    Route::get('/auth/google', 'Auth\LoginController@handleProviderCallback');
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
    Route::group(["prefix"=>"/password/reset"],function(){
        Route::get('/',function(Request $request){
            if($request->wantsJson()){
                return response()->json(['status' => 'success', 'message' => 'OK']);
            }
            return view();
        });
        Route::get('/change',function(){
            return getView();
        });
    });

    //API only auth user
    Route::get('/profile','Page\HomeController@showProfile')->where('any','.*');
    Route::get('/dashboard','Page\HomeController@dashboard')->where('any','.*');
    Route::group(["prefix"=>"/users"],function(){
        Route::post('/login','Auth\LoginController@login');
        Route::post('/register','Auth\RegisterController@register');
        Route::post('/register/google', 'UserController@createGoogleUser');
        Route::post('/logout','UserController@logout');
        Route::get('/pengaturan',function(){
            return getView('pengaturan');
        });
        Route::group(['prefix'=>'/update'], function(){
            Route::put('/profile','UserController@updateProfile');
            Route::put('/password','UserController@updatePassword');
        });
    });
    Route::group(["prefix"=>"/device"],function(){
        Route::get('/', 'Page\DeviceController@showDevice');
        Route::get('/tambah',function(Request $request){
            if($request->wantsJson()){
                return response()->json(['status' => 'success', 'message' => 'OK']);
            }
            return getView('device.tambah');
        });
        Route::get('/{any}', 'Page\DeviceController@detailDevice');

        Route::post('/create', 'Services/DeviceController@createDevice');
        Route::post('/register', 'Services/DeviceController@registerDevice');
        Route::put('/{id}', 'Services/DeviceController@updateDevice');
        Route::delete('/{id}', 'Services/DeviceController@deleteDevice');
    });
    Route::group(["prefix"=>"/firmware"],function(){
        Route::get('/', 'Page\FirmwareController@showFirmware');
        Route::get('/tambah',function(Request $request){
            if($request->wantsJson()){
                return response()->json(['status' => 'success', 'message' => 'OK']);
            }
            return getView('firmware.tambah');
        });
        Route::get('/{id}', 'Page\FirmwareController@detailFirmware');

        Route::post('/', 'Services/FirmwareController@createFirmware');
        Route::put('/update', 'Services/FirmwareController@updateFirmware');
        Route::delete('/', 'Services/FirmwareController@deleteFirmware');
    });
    // Route::group(["prefix"=>"/laporan"],function(){
    //         Route::get('/', 'Page\LaporanController@');
    //         Route::get('/{id}', 'Page\LaporanController@');
    //     Route::get('/tambah',function(Request $request){
    //         if($request->wantsJson()){
    //             return response()->json(['status' => 'success', 'message' => 'OK']);
    //         }
    //         return getView('laporan.tambah');
    //     });
    //     Route::post('/register', 'Services/LaporanController@');
    //     Route::post('/activate', 'Services/LaporanController@');
    //     Route::put('/update', 'Services/LaporanController@');
    //     Route::delete('/delete', 'Services/LaporanController@');
    // });

    //API only admin
    Route::group(["prefix"=>"/admin"],function(){
        Route::get('/', 'AdminController@showAdmin');
        Route::get('/tambah',function(Request $request){
            if($request->wantsJson()){
                return response()->json(['status' => 'success', 'message' => 'OK']);
            }
            return getView('admin.tambah');
        });
        Route::get('/{any}', 'AdminController@detailAdmin');

        Route::post('/create', 'AdminController@createAdmin');
        Route::put('/{id}', 'AdminController@updateAdmin');
        Route::delete('/{id}', 'AdminController@deleteAdmin');
    });
});
Route::fallback(function(){
    $indexPath = public_path('dist/index.html');
    if (File::exists($indexPath)) {
        $htmlContent = File::get($indexPath);
        return response($htmlContent, 404);
    } else {
        // If the index.html file doesn't exist, return a 404 response
        return response()->json(['error' => 'Page not found'], 404);
    }
});
?>