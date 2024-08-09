<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class DeviceController extends Controller
{
    public function firstTime(Request $request){
        $validator = Validator::make($request->all(), [
            'id_device'=>'required',
            'gps'=>'nullable'
        ],[
            'id_device.required'=>'ID device harus di isi',
            'token.required'=>'Token device harus di isi',
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
    public function createDevice(Request $request){
        $validator = Validator::make($request->only('nama_device', 'device_id', 'token', 'activated'), [
            'nama_device'=>'required|min:3|max:30',
            'device_id'=>'required|max:50',
            'token'=>'required|max:30',
            'activated'=>'required|boolean',
        ],[
            'nama_device.required'=>'Nama device harus di isi',
            'device_id.required'=>'ID device harus di isi',
            'token.required'=>'Token device harus di isi',
            'activated.required'=>'Status Device harus di isi',
            'activated.boolean'=>'Status Device harus boolean',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $createDevice = Device::insertGetId([
            'nama_device' => $request->input('token'),
            'device_id' => $request->input('token'),
            'device_token' => $request->input('token'),
            'activated' => $request->input('activated'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        if($createDevice && $createDevice > 0){
            return response()->json(['status'=>'error','message'=>'Gagal menambahkan data Device'], 500);
        }
        Schema::dropIfExists('laporan_'.$createDevice);
        Schema::create('laporan_'.$createDevice, function (Blueprint $table) use($createDevice){
            $table->id('id_laporan');
            $table->integer('organik');
            $table->integer('anorganik');
            $table->timestamps();
            $table->unsignedBigInteger('id_device');
            $table->foreign('id_device', "fk_laporan_id".$createDevice)->references('id_device')->on('device');
        });
        return response()->json(['status'=>'success','message'=>'Data Device berhasil di perbarui']);
    }
    public function updateDevice(Request $request){
        $validator = Validator::make($request->only('nama_device', 'device_id', 'token', 'activated'), [
            'id_device'=>'required', 
            'nama'=>'nullable',
        ],[
            'id_device.required'=>'id_device harus di isi',
            'token.required'=>'token device harus di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $device = Device::select('activated')->where('uuid', $request->input('id_firmware'))->limit(1)->first();
        if (is_null($device)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        //update device
        $updatedDevice = Device::where('uuid', $request->input('id_device'))->update([
            'nama_device' => $request->input('token'),
            'device_id' => $request->input('token'),
            'device_token' => $request->input('token'),
            'activated' => $request->input('activated'),
            'updated_at' => Carbon::now(),
        ]);
        if (!$updatedDevice) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data Device'], 500);
        }
        return response()->json(['status' =>'success','message'=>'Data Device berhasil di perbarui']);
    }
    public function deleteDevice(Request $request){
        $validator = Validator::make($request->only('id_device'), [
            'id_device' => 'required'
        ],[
            'id_device.required' => 'ID Device must filled !'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $device = Device::select('id_device')->where('uuid', $request->input('id_firmware'))->limit(1)->first();
        if (is_null($device)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        if (!Device::where('uuid', $request->input('id_firmware'))->delete()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data device'], 500);
        }
        Schema::dropIfExists('laporan_'.$device->id_device);
        return response()->json(['status'=>'error', 'message'=>'Data device berhasil dihapus']);
    }
    //data from esp32
    public function addLaporan(Request $request, Device $device){
        $validator = Validator::make($request->all(), [
            'organik'=>'required',
            'anorganik'=>'required'
        ],[
            'organik.required'=>'data organik harus di isi',
            'anorganik.required'=>'data anorganik harus di isi',
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
            'email.required'=>'Email harus di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'id_device.required'=>'id device harus di isi',
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