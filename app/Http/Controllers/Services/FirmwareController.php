<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Device;
use App\Models\Firmware;
class FirmwareController extends Controller
{
    public function get(Request $request){
        $update = Firmware::where('version', '>', $request->input('current_version'))->first();
        if ($update) {
            return response()->json(['update_version' => $update->version]);
        } else {
            return response()->json(['update_version' => null]);
        }
    }
    public function createFirmware(Request $request){
        $validator = Validator::make($request->only('uuid','judul', 'deskripsi', 'link_video', 'rentang_usia', 'foto'), [
        ], [
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
         //process file
        if (!$request->hasFile('firmware')) {
            return response()->json(['status'=>'error','message'=>'Firmware Must Filled !'], 400);
        }
        $file = $request->file('firmware');
        if(!($file->isValid() && in_array($file->extension(), ['bin']))){
            return response()->json(['status'=>'error','message'=>'Invalid Format Firmware !'], 400);
        }
        $firmwareName = $file->hashName();
        $fileData = Crypt::encrypt(file_get_contents($file));
        Storage::disk('firmware')->put('version/' . $firmwareName, $fileData);
        //add firmware
        $createFirmware = Firmware::insert([
            'version' => $request->input('version'),
        ]);
        if (!$createFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'']);
    }
    public function updateFirmware(Request $request){
        $validator = Validator::make($request->only('uuid','judul', 'deskripsi', 'link_video', 'rentang_usia', 'foto'), [
        ], [
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $firmware = Firmware::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
        if (is_null($firmware)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
        }
        //process file
        if (!$request->hasFile('firmware')) {
            return response()->json(['status'=>'error','message'=>'Firmware Must Filled !'], 400);
        }
        $file = $request->file('firmware');
        if(!($file->isValid() && in_array($file->extension(), ['bin']))){
            return response()->json(['status'=>'error','message'=>'Invalid Format Firmware !'], 400);
        }
        $destinationPath = storage_path('app/firmware/');
        $fileToDelete = $destinationPath . $firmware->ktp_seniman;
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete('/version/'.$firmware->ktp_seniman);
        $ktpName = $file->hashName();
        $fileData = Crypt::encrypt(file_get_contents($file));
        Storage::disk('firmware')->put('version/1111/' . $ktpName, $fileData);
        //update firmware
        $updateFirmware = Firmware::update([
            'version' => $request->input('version'),
        ]);
        if (!$updateFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'']);
    }
    public function deleteFirmware(Request $request){
        $validator = Validator::make($request->only('uuid','judul', 'deskripsi', 'link_video', 'rentang_usia', 'foto'), [
        ], [
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $disi = Firmware::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
        if (is_null($disi)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
        }
        //delete file
        $destinationPath = storage_path('app/firmware/');
        $fileToDelete = $destinationPath . $firmware->ktp_seniman;
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete('ktp_seniman/'.$firmware->ktp_seniman);
        $deleteFirmware = Firmware::delete([
            'version' => $request->input('version'),
            'version' => $request->input('version'),
            'version' => $request->input('version'),
        ]);
        if (!$deleteFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'']);
    }
}