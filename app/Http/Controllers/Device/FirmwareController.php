<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Firmware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class FirmwareController extends Controller
{
    private static $allDevice;
    public function __construct(){
        self::$allDevice = ['arduino', 'esp32'];
    }
    public function checkUpdate(Request $request){
        $validator = Validator::make($request->only('version'), [
            'esp32' => 'required',
            'arduino' => 'required',
        ],[
            'esp32.required' => 'esp32 harus di isi',
            'arduino.required' => 'arduino harus di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $firmwareData = Firmware::where('version', '>', $request->input('current_version'))->first();
        if (is_null($firmwareData)) {
            return response()->json(['status' => 'error', 'message' => 'Data firmware tidak ditemukan'], 404);
        }
        $dataRes = [
            'esp32' => ['message' => 'none', 'version' => ''],
            'arduino' => ['message' => 'none', 'version' => ''],
        ];
        if($firmwareData == 'esp32'){
            $dataRes['esp32']['message'] = 'ada update';
            $dataRes['esp32']['version'] = $firmwareData['esp32']['version'];
        }else if($firmwareData == 'arduino'){
            $dataRes['arduino']['message'] = 'ada update';
            $dataRes['arduino']['version'] = $firmwareData['arduino']['version'];
        }
        return response()->json(['status'=>'error', 'data' => $dataRes]);
    }
    public function downloadFirmware(Request $request){
        $validator = Validator::make($request->only('id_firmware', 'device'), [
            'id_firmware' => 'required',
            'device' => 'required',
        ], [
            'id_firmware.required' => 'ID Firmware must filled !',
            'device.required' => 'Device must filled !',
            'device.in' => 'Device must esp32 or arduino !',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        foreach(['name', 'version'] as $key){
            if(!array_key_exists($key, $request->input('device'))){
                return response()->json(['status' => 'error', 'message' => 'Invalid device object'], 400);
            }
        }
        if(!in_array($request->input('device')['name'], self::$allDevice)){
            return response()->json(['status' => 'error', 'message' => 'Invalid name device'], 400);
        }
        $firmwareData = Firmware::select('file', 'version', 'device')->where('device', $request->input('device')['name'])->where('version', $request->input('device')['version'])->first();
        if (is_null($firmwareData)) {
            return response()->json(['status' => 'error', 'message' => 'Data firmware tidak ditemukan'], 404);
        }
        $filePath = storage_path('app/firmware/'. $request->input('device')['name'] . '/' . $firmwareData['file']);
        if (!file_exists($filePath)){
            return response()->json(['status'=>'error', 'message'=>'Firmware Not Found'], 500);
        }
        $tempFilePath = tempnam(sys_get_temp_dir(), 'decrypted_file');
        file_put_contents($tempFilePath, Crypt::decrypt(Storage::disk('firmware')->get($request->input('device')['name'] . '/' . $firmwareData->file)));
        return Response::download($tempFilePath, $firmwareData->version. '.' . pathinfo($firmwareData->file, PATHINFO_EXTENSION));
    }
}