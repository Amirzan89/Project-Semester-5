<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class DeviceController extends Controller
{
    public function fetchData(Request $request){
        return response()->json(['']);
    }
}