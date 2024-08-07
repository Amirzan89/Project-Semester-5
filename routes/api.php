<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Device\DeviceController;

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
Route::group(["prefix"=>"/firmware"],function(){
    Route::post('/check','Device\FirmwareController@checkUpdate');
    Route::post('/download','Device\FirmwareController@downloadUpdate');
});
Route::group(["prefix"=>"/device"],function(){
    Route::post('/create', 'Device\DeviceController@createForgotPassword');
    Route::post('/update', 'Device\DeviceController@createForgotPassword');
});
Route::group(["prefix"=>"/laporan"],function(){
    Route::get('/check', 'Services/FirmwareController');
    Route::post('/register', 'Services/FirmwareController');
    Route::post('/create', 'Services/FirmwareController');
    Route::put('/update', 'Services/FirmwareController');
    Route::delete('/delete', 'Services/FirmwareController');
});