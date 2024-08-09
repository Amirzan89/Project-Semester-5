<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Firmware;
use Carbon\Carbon;
class FirmwareController extends Controller
{
    public function downloadFirmware(Request $request){
        $validator = Validator::make($request->only('id_device'), [
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
        $event = Firmware::select('nama_event','poster_event')->where('id_event',$request->input('id_event'))->join('detail_events', 'events.id_detail', '=', 'detail_events.id_detail')->limit(1)->get()[0];
        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Data Firmware tidak ditemukan'], 404);
        }
        $filePath = storage_path("app/event/{$event->poster_event}");
        if (!file_exists($filePath)) {
            return response()->json(['status' => 'error', 'message' =>'File Firmware tidak ditemukan'], 404);
        }
        $tempFilePath = tempnam(sys_get_temp_dir(), 'decrypted_file');
        file_put_contents($tempFilePath, Crypt::decrypt(Storage::disk('event')->get("poster_event/{$event->poster_event}")));
        return Response::download($tempFilePath, $event->nama_event. '.' .pathinfo($event->poster_event, PATHINFO_EXTENSION));
    }
    public function createFirmware(Request $request){
        $validator = Validator::make($request->only('name', 'description', 'version', 'release_date', 'checksum', 'device', 'file'), [
            'name' => 'required|min:3|max:30',
            'descrtiption' => 'required|max:4000',
            'version' => 'required',
            'release_date' => ['required', 'date', 'before_or_equal:' . now()->toDateString()],
            'checksum' => 'required',
            'device' => 'required|in:esp32,arduino',
            'file' => 'required|file|mimes:bin|max:5120'
        ], [
            'name.required' => 'Name Firmware must filled !',
            'name.min' => 'Name minimal 3 karakter',
            'name.max' => 'Name maksimal 30 karakter',
            'descrtiption.required' => 'Description must filled !',
            'descrtiption.max' => 'Description maksimal 4000 karakter',
            'version.required' => 'Version must filled !',
            'release_date.required' => 'Release date must filled !',
            'release_date.date' => 'Format release date tidak valid',
            'release_date.before_or_equal' => 'Release date harus Sebelum dari tanggal sekarang',
            'checksum.required' => 'Checksum must filled !',
            'device.required' => 'Devie must filled !',
            'device.in' => 'Device harus esp32 atau Arduino  ',
            'file.required' => 'File must filled !',
            'file.file' => 'File harus berupa file !',
            'file.mimes' => 'Format file tidak valid. Gunakan format bin',
            'file.max' => 'Ukuran file maksimal 5MB !',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
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
        $checksum = hash('', file_get_contents($file));
        $fileData = Crypt::encrypt(file_get_contents($file));
        Storage::disk('firmware')->put('version/' . $firmwareName, $fileData);
        //add firmware
        $createFirmware = Firmware::insert([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $firmwareName,
            'checksum' => $request->input('checksum'),
            'download_url' => $request->input('download_url'),
            'device' => $request->input('device'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        if (!$createFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil ditambah']);
    }
    public function updateFirmware(Request $request){
        $validator = Validator::make($request->only('id_firmware', 'name', 'description', 'version', 'release_date', 'checksum', 'device', 'file'), [
            'id_firmware' => 'required',
            'name' => 'required|min:3|max:30',
            'descrtiption' => 'required|max:4000',
            'version' => 'required',
            'release_date' => ['required', 'date', 'before_or_equal:' . now()->toDateString()],
            'checksum' => 'required',
            'device' => 'required|in:esp32,arduino',
            'file' => 'required|file|mimes:bin|max:5120'
        ], [
            'id_firmware.required' => 'ID Firmware must filled !',
            'name.required' => 'Name Firmware must filled !',
            'name.min' => 'Name minimal 3 karakter',
            'name.max' => 'Name maksimal 30 karakter',
            'descrtiption.required' => 'Description must filled !',
            'descrtiption.max' => 'Description maksimal 4000 karakter',
            'version.required' => 'Version must filled !',
            'release_date.required' => 'Release date must filled !',
            'release_date.date' => 'Format release date tidak valid',
            'release_date.before_or_equal' => 'Release date harus Sebelum dari tanggal sekarang',
            'checksum.required' => 'Checksum must filled !',
            'device.required' => 'Devie must filled !',
            'device.in' => 'Device harus esp32 atau Arduino  ',
            'file.required' => 'File must filled !',
            'file.file' => 'File harus berupa file !',
            'file.mimes' => 'Format file tidak valid. Gunakan format bin',
            'file.max' => 'Ukuran file maksimal 5MB !',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $firmware = Firmware::select('version')->where('uuid', $request->input('id_firmware'))->limit(1)->first();
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
        $destinationPath = storage_path('app/firmware/version/');
        $fileToDelete = $destinationPath . $firmware->ktp_seniman;
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete('/version/'.$firmware->ktp_seniman);
        $hFile = $file->hashName();
        $fileData = Crypt::encrypt(file_get_contents($file));
        Storage::disk('firmware')->put('version/' . $hFile, $fileData);
        //update firmware
        $updateFirmware = $firmware->where('uuid', $request->input('id_firmware'))->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $hFile,
            'checksum' => $request->input('checksum'),
            'download_url' => $request->input('download_url'),
            'device' => $request->input('device'),
            'updated_at' => Carbon::now(),
        ]);
        if (!$updateFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengubah data Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil diupdate']);
    }
    public function deleteFirmware(Request $request){
        $validator = Validator::make($request->only('id_firmware'), [
            'id_firmware' => 'required'
        ], [
            'id_firmware.required' => 'ID Firmware must filled !'
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
        //delete file
        $destinationPath = storage_path('app/firmware/');
        $fileToDelete = $destinationPath . $firmware->ktp_seniman;
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete('ktp_seniman/'.$firmware->ktp_seniman);
        if (!Firmware::where('uuid', $request->input('id_firmware'))->delete()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil dihapus']);
    }
}