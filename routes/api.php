<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware'=>'auth.device'],function(){
    Route::group(["prefix"=>"/firmware"],function(){
        Route::post('/check','Device\FirmwareController@checkUpdate');
        Route::post('/download','Device\FirmwareController@downloadUpdate');
    });
    Route::group(["prefix"=>"/device"],function(){
        Route::post('/upload', 'Device\DeviceController@handleData');
    });
});