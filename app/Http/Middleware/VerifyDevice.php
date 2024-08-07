<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Device;
use Closure;
class VerifyDevice
{
    public function handle(Request $request, Closure $next){
        $validator = Validator::make($request->all(), [
            'id_device'=>'required',
            'token'=>'required',
        ],[
            'id_device.required'=>'id device wajib di isi',
            'token.required'=>'token wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors = $errorMessages[0];
            }
            return response()->json(['status' => 'error', 'message' => $errors], 400);
        }
        $idDevice = $request->input('id_device');
        $token = $request->input('token');
        //verify id and token
        if(Device::whereRaw('BINARY id_device = ?', [$idDevice])->limit(1)->exists()){
            if(Device::select('token')->whereRaw('BINARY id_device = ?',[$idDevice])->limit(1)->exists()){
                return$next($request);
            }else{
                return response()->json(['status'=>'error','message'=>'invalid token'],401);
            }
        }else{
            return response()->json(['status'=>'error','message'=>'device not found'],401);
        }
    }
}
