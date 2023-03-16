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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/terserah','LoginController@Login');
$router->get('/',function() use ($router){
    return view('home');
});
$router->get('/mboh',function() use ($router){
    // return view('home');
});
Route::group(['middleware'=>'auth'],function(){
    Route::post('/logout',function(){
        // return view('login');
    });
    Route::get('/login',function(){
        return view('login');
    });
    Route::get('/register',function(){
        return view('register');
    });
    Route::get('/dashboard',function(){
        return view('dashboard');
    });
    // Route::post('/login-form','Login@Login');
    Route::post('/login-form','LoginController@Login');
    Route::post('/register-form','RegisterController@Register');
    Route::group(["prefix"=>"/user"],function(){
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