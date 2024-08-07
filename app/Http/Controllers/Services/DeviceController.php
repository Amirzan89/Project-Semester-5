<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
Use Closure;
class DeviceController extends Controller
{
    public function getGps(Request $request){
        $address = $request->input('');
        try {
            
            $response = Http::get('http://esp32_ip_address');
            if ($response->successful()) {
                $responseData = $response->json();
                // Handle the response from the ESP32
                // ...
                return response()->json(['message' => 'Data sent successfully']);
            } else {
                return response()->json(['message' => 'Failed to send data to ESP32'], 500);
            }
        } catch (RequestException $exception) {
            return response()->json(['message' => 'Error sending request to ESP32: ' . $exception->getMessage()], 500);
        }
    }
    public function firstTime(Request $request){
        $validator = Validator::make($request->all(), [
            'id_device'=>'required',
            'gps'=>'nullable'
        ],[
            'id_device.required'=>'ID device wajib di isi',
            'token.required'=>'Token device wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $idDevice = $request->input('id_device');
        $updateData = ['actived'=>true,'updated_at' => Carbon::now()];
        if(is_null(DB::table('device')->whereRaw("BINARY id_device = ?",[$idDevice])->update($updateData))){
            return response()->json(['status'=>'error','message'=>'data failed edit'],401);
        }else{
            return response()->json(['status'=>'success','message'=>'data success edit']);
        }
    }
    // protected function createIdDevice(){
    //     $InDevice = Device::select('id_device')->limit(1)->get();
    //     $idDevice = json_decode($InDevice);
    //     if(empty($InDevice) || is_null('')){
    //         $id = 'DVC'.'';
    //         return $id;
    //     }else{
    //         $id = 'DVC'.'';
    //         return $id;
    //     }
    // }
    public function createDevice(Request $request, Device $device){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'id_device'=>'required',
            'nama_device'=>'required',
            'token'=>'required',
            'gps'=>'nullable'
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'id_device.required'=>'ID device wajib di isi',
            'nama_device.required'=>'Nama device wajib di isi',
            'token.required'=>'Token device wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $email = $request->input('email');
        $idDevice = $request->input('id_device');
        $token = $request->input('token');
        $nama = $request->input('nama_device');
        //check email
        if(User::whereRaw("BINARY email = ?",[$email])->limit(1)->exists()){
            if(is_null($request->input('gps')) || empty($request->input('gps'))){
                $device->id_device = $idDevice;
                $device->nama_device = $nama;
                $device->email = $email;
                $device->token = $token;
                $device->gps = Str::random(32);
                $device->actived = false;
            }else{
                $device->id_device = $idDevice;
                $device->nama_device = $nama;
                $device->email = $email;
                $device->token = $token;
                $device->gps = $request->input('gps');
                $device->actived = false;
            }
            if($device->save()){
                Schema::dropIfExists('laporan_'.$device->id);
                Schema::create('laporan_'.$device->id, function (Blueprint $table) use($device){
                    $table->id('id_laporan');
                    $table->integer('organik');
                    $table->integer('anorganik');
                    $table->timestamps();
                    $table->unsignedBigInteger('id_device');
                    $table->foreign('id_device', "fk_laporan_id".$device->id)->references('id')->on('device');
                });
                return response()->json(['status'=>'success','message'=>'success add device']);
            }else{
                return response()->json(['status'=>'error','message'=>'fail add device'],401);
            }
        }else{
            return response()->json(['status'=>'error','message'=>'email not found'],401);
        }
    }
    public function getDevice(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'query'=>'nullable',
            'limit'=>'nullable',
            'mode'=>'nullable',
            'count'=>'nullable'
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $email = $request->input('email');
        if(User::select("email")->whereRaw("BINARY email = ?",[$email])->limit(1)->exists()){
            if($request->input('mode') == 'count'){
                $totalDb = Device::select()->whereRaw("BINARY email = ?",[$email])->count($request->input('count','*'));
                $total = json_decode(json_encode($totalDb));
                return response()->json(['status'=>'success','data'=>$total]);
            }else{
                $query = "SELECT ".$request->input('query','*')." FROM device WHERE BINARY email = '$email' ".$request->input('orderby','')." LIMIT ".$request->input('limit',1);
                $dataDb = DB::select($query);
                $data = json_decode(json_encode($dataDb));
                if(empty($data) || is_null($data)){
                    return response()->json(['status'=>'success','data'=>null]);
                }
                return response()->json(['status'=>'success','data'=>$data]);
            }
        }else{
            return response()->json(['status'=>'error','message'=>'email not found'],401);
        }
    }
    public function updateDevice(Request $request, Response $response, Device $device){
        $validator = Validator::make($request->all(), [
            'id_device'=>'required', 
            'nama'=>'nullable',
            'token'=>'required',
            'token_new'=>'nullable',
            'gps'=>'nullable'
        ],[
            'id_device.required'=>'id_device wajib di isi',
            'token.required'=>'token device wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $idDevice = $request->input('id_device');
        $nama = $request->input('nama');
        $tokenNew = $request->input('token_new');
        $gps = $request->input('gps');
        $updateData = ['updated_at' => Carbon::now()];
        if (!is_null($nama)) {
            $updateData['nama_device'] = $nama;
        }
        if (!is_null($gps)) {
            $updateData['gps'] = $gps;
        }
        if (!is_null($tokenNew)) {
            $updateData['token'] = $tokenNew;
        }
        if(is_null(DB::table('device')->whereRaw("BINARY id_device ?",[$idDevice])->update($updateData))){
            return response()->json(['status'=>'success','message'=>'data success updated']);
        }else{
            return response()->json(['status'=>'error','message'=>'data failed update'],401);
        }
    }
    public function deleteDevice(Request $request, Response $response, Device $device){
        $idDevice = $request->input('id_device');
        $token = $request->input('token');
        Schema::dropIfExists('laporan_'.$idDevice);
        $deleted = DB::table('device')->whereRaw("BINARY id_device = ? AND BINARY token = ?",[$idDevice, $token])->delete();
        if($deleted){
            return response()->json(['status'=>'success','message'=>"success delete device with id $idDevice"]);
        }else{
            return response()->json(['status'=>'error','message'=>"failed delete device with id $idDevice"],500);
        }
    }
    //data from esp32
    public function addLaporan(Request $request, Device $device){
        $validator = Validator::make($request->all(), [
            'organik'=>'required',
            'anorganik'=>'required'
        ],[
            'organik.required'=>'data organik wajib di isi',
            'anorganik.required'=>'data anorganik wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $idDevice = $request->input('id_device');
        $organik = $request->input('organik');
        $anorganik = $request->input('anorganik');
        $id = DB::select("SELECT id FROM device WHERE id_device = '".$request->input('id_device')."' LIMIT 1");
        if (Schema::hasTable('laporan_'.$id[0]->id)) {
            DB::table('laporan_'.$id[0]->id)->insert([
                'organik' => $organik,
                'anorganik' => $anorganik,
                'id_device'=>$id[0]->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return response()->json(['status'=>'success','message'=>'success add data']);
        }else{
            return response()->json(['message' => 'Table invalid'], 400);
        }
    }
    public function getLaporan(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'id_device'=>'required',
            'query'=>'nullable',
            'limit'=>'nullable',
            'mode'=>'nullable',
            'count'=>'nullable',
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'id_device.required'=>'id device wajib di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $email = $request->input('email');
        if(User::select("email")->whereRaw("BINARY email = ?",[$email])->limit(1)->exists()){
            $id = DB::select("SELECT id FROM device WHERE id_device = '".$request->input('id_device')."' LIMIT 0,1");
            if($request->input('mode') == 'count'){
                $dataDb = DB::table('laporan_'.$id[0]->id)->select($request->input('query','*'))->count($request->input('count','*'));
                $data = json_decode(json_encode($dataDb));
                return response()->json(['status'=>'success','data'=>$data]);
            }else{
                $query = "SELECT ".$request->input('query','*')." FROM laporan_".$id[0]->id.' '.$request->input('orderby','')." LIMIT ".$request->input('limit',1);
                $dataDb = DB::select($query);
                if(empty($dataDb)){
                    return response()->json(['status'=>'error','message'=>'data not found'],404);
                }else{
                    $data = json_decode(json_encode($dataDb));
                    return response()->json(['status'=>'success','data'=>$data]);
                }
            }
        }else{
            return response()->json(['status'=>'error','message'=>'email not found'],401);
        }
    }
}
?>