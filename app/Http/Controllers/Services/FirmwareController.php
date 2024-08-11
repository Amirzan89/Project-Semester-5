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
use ZipArchive;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;
class FirmwareController extends Controller
{
    private static $allDevice;
    public function __construct(){
        self::$allDevice = ['arduino', 'esp32'];
    }
    private function get(){
    }
    private function setZip($data, $file, $nameZip){
        $tempSign = tempnam(sys_get_temp_dir(), 'signature.json');
        file_put_contents($tempSign, $data['signature']);
        $tempChecksum = tempnam(sys_get_temp_dir(), 'checksum.json');
        file_put_contents($tempChecksum, $data['checksum']);
        $tempManifest = tempnam(sys_get_temp_dir(), 'manifest.json');
        file_put_contents($tempManifest, json_encode($data['manifest']));
        //encrypt firmware
        $encryptionKey = openssl_random_pseudo_bytes(32);
        $aes = new AES('aes-256-cbc');
        $aes->setKey($encryptionKey);
        $aes->setIV(openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')));
        $encryptedFirmware = $aes->encrypt(file_get_contents($file));
        // Encrypt the encryption key using the public key
        $publicKey = RSA::load(file_get_contents('random.pem'));
        $publicKey->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $encryptedKey = $publicKey->encrypt($encryptionKey);
        $tempZip = tempnam(sys_get_temp_dir(), "$nameZip.zip");
        $zip = new ZipArchive();
        $zip->open($tempZip, ZipArchive::CREATE);
        $zip->addFile($tempSign, 'firmware/signature.json');
        $zip->addFile($tempChecksum, 'firmware/checksum.json');
        $zip->addFile($tempManifest, 'firmware/manifest.json');
        $zip->addFile($encryptedFirmware, "firmware/$nameZip.bin");
        $zip->addFile($encryptedKey, "key/$nameZip.key");
        // Close the zip archive
        $zip->close();
        unlink($tempManifest);
        unlink($tempSign);
        unlink($tempChecksum);
        return $tempZip;
    }
    private function decrypt(){
        //verify
        // Extract the firmware and checksum from the ZIP archive
        // $firmwareData = file_get_contents('firmware/' . $firmwareName);
        // $firmwareHash = hash('sha256', $firmwareData);
        // $expectedChecksum = $data['checksum'];

        // // Verify the checksum
        // if ($firmwareHash !== $expectedChecksum) {
        //     throw new Exception('Checksum mismatch');
        // }

        // // Extract the signature from the ZIP archive
        // $signature = $data['signature'];

        // // Load the public key
        // $publicKey = new RSA();
        // $publicKey->load(file_get_contents('path/to/public_key.pem'));

        // // Verify the signature
        // if (!$publicKey->verify($firmwareHash, $signature)) {
        //     throw new Exception('Signature mismatch');
        // }
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
        if(!in_array($request->input('device'), self::$allDevice)){
            return response()->json(['status' => 'error', 'message' => 'Invalid name device'], 400);
        }
         //process file
        if (!$request->hasFile('firmware')) {
            return response()->json(['status'=>'error','message'=>'Firmware Must Filled !'], 400);
        }
        $file = $request->file('firmware');
        if(!($file->isValid() && in_array($file->extension(), ['bin']))){
            return response()->json(['status'=>'error','message'=>'Invalid Format Firmware !'], 400);
        }
        $signature = '';
        $checksum = hash('sha512', file_get_contents($file));
        $manifest = [
            'name' => $request->input('name'),
            'release_notes' => $request->input('version'),
            'device' => $request->input('device'),
            'version' => $request->input('version'),
            'release_date' => $request->input('version'),
        ];
        $pathZip = self::setZip(['checksum'=>$checksum, 'signature'=>$signature, 'manifest'=>$manifest], $file, $request->input('version'));
        Storage::disk('firmware')->move($pathZip, $request->input('device') . '/' . $request->input('version') . '.zip');
        //add firmware
        $createFirmware = Firmware::insert([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $request->input('version') . '.zip',
            'checksum' => $checksum,
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
        if(!in_array($request->input('device'), self::$allDevice)){
            return response()->json(['status' => 'error', 'message' => 'Invalid name device'], 400);
        }
        $firmware = Firmware::select('file')->where('uuid', $request->input('id_firmware'))->first();
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
        $filePath = storage_path('app/firmware/'. $request->input('device') . '/' . $firmware['file']);
        if (file_exists($filePath) && !is_dir($filePath)) {
            unlink($filePath);
        }
        $signature = '';
        $checksum = hash('sha512', file_get_contents($file));
        $manifest = [
            'name' => $request->input('name'),
            'release_notes' => $request->input('version'),
            'device' => $request->input('device'),
            'version' => $request->input('version'),
            'release_date' => $request->input('version'),
        ];
        Storage::disk('firmware')->delete($request->input('device') . '/' . $firmware->file);
        $pathZip = self::setZip(['checksum'=>$checksum, 'signature'=>$signature, 'manifest'=>$manifest], $file, $request->input('version'));
        Storage::disk('firmware')->move($pathZip, $request->input('device') . '/' . $request->input('version') . '.zip');
        //update firmware
        $updateFirmware = $firmware->where('uuid', $request->input('id_firmware'))->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $firmwareName,
            'checksum' => $request->input('checksum'),
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
            'id_firmware' => 'required',
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
        $firmware = Firmware::select('device', 'file')->where('uuid', $request->input('id_firmware'))->first();
        if (is_null($firmware)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
        }
        //delete file
        $destinationPath = storage_path('app/firmware/');
        $fileToDelete = $destinationPath . $firmware->file;
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete($firmware->device . '/' . $firmware->file);
        if (!Firmware::where('uuid', $request->input('id_firmware'))->delete()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil dihapus']);
    }
}