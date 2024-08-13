<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Device;
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
    private function generateAsymKey(){
        $config = array(
            'config' => 'openssl.cnf',
            'default_md' => 'sha512',
            'private_key_bits' => '512',
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        );
        $keypair =  openssl_pkey_new($config);
        openssl_pkey_export($keypair, $privKey, null, $config);
        $publicKey = openssl_pkey_get_details($keypair);
        $pubKey = $publicKey['key'];
        return ['random1'=>$pubKey, 'random2'=>$privKey];
    }
    public static function setZip($deviceData, $opt = 'single'){
        $composable = function($deviceData, &$zip, $cond = 'single'){
            $filePath = storage_path("app/firmware/" . $deviceData['device'] . "/" . $deviceData['file']);
            if (!file_exists($filePath)) {
                return response()->json(['status' => 'error', 'message' => 'File device tidak ditemukan'], 404);
            }
            $file = Crypt::decrypt(Storage::disk('firmware')->get($deviceData['device'] . "/" . $deviceData['file']));
            $prefix = '';
            if($cond == 'each'){
                $prefix = $deviceData['device'] . '/';
            }
            $manifest = [
                'name' => $deviceData['name'],
                'release_notes' => $deviceData['description'],
                'device' => $deviceData['device'],
                'version' => $deviceData['version'],
                'release_date' => $deviceData['release_date'],
            ];
            $tempManifest = tempnam(sys_get_temp_dir(), 'manifest.json');
            file_put_contents($tempManifest, json_encode($manifest, JSON_PRETTY_PRINT));
            //get checksum
            $tempChecksum = tempnam(sys_get_temp_dir(), 'checksum.json');
            file_put_contents($tempChecksum, $deviceData['checksum']);
            //getKey
            $keys = self::generateAsymKey();
            //create signature
            $privKey = $keys['random2'];
            // $privKey = file_get_contents('private_key.pem');
            $privateKey = openssl_pkey_get_private($privKey);
            openssl_sign($file, $signature, $privateKey, 'sha512');
            $tempSign = tempnam(sys_get_temp_dir(), 'signature.json');
            file_put_contents($tempSign, $signature);
            //get file
            $tempFilePath = tempnam(sys_get_temp_dir(), 'decrypted_file');
            file_put_contents($tempFilePath, $file);
            //encrypt firmware
            $encryptionKey = openssl_random_pseudo_bytes(32);
            $aes = new AES('aes-256-cbc');
            $aes->setKey($encryptionKey);
            $aes->setIV(openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')));
            $encryptedFirmware = $aes->encrypt(file_get_contents($tempFilePath));
            openssl_public_encrypt($encryptionKey, $encFirmKey, $keys['random1']);
            $tempKey = tempnam(sys_get_temp_dir(), 'firm.key');
            file_put_contents($tempKey, $encFirmKey);
            //make privKey
            $tempRandomKey = tempnam(sys_get_temp_dir(), 'random.key');
            $aes = new AES('aes-256-cbc');
            $aes->setKey(hash('sha256', $deviceData['device_id']. '=='.$deviceData['device_token']));
            $aes->setIV(openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')));
            $encRankey = $aes->encrypt($privKey);
            file_put_contents($tempRandomKey, $encRankey);
            $zip->addFile($tempSign, "firmware/".$prefix."signature.json");
            $zip->addFile($tempChecksum, "firmware/".$prefix."checksum.json");
            $zip->addFile($tempManifest, "firmware/".$prefix."xmanifest.json");
            $zip->addFile($tempKey, "firmware/$prefix" . $deviceData['version'] . "_firm.key");
            $zip->addFile($encryptedFirmware, "firmware/$prefix" . $deviceData['version'] . ".bin");
            $zip->addFile($tempRandomKey, "firmware/" . $deviceData['version'] . "_random.key");
            unlink($tempKey);
            unlink($tempSign);
            unlink($tempChecksum);
            unlink($tempManifest);
            unlink($tempFilePath);
        };
        $valCol = $deviceData['device'];
        if(!in_array($valCol, self::$allDevice)){
            return ['status' => 'error', 'message' => 'Invalid name device'];
        }
        $firmwareDB = Firmware::select('name', 'description', 'version', 'release_date', 'file', 'checksum', 'device')->whereHas('roles', function ($query) use ($valCol) {
            $query->whereIn('name', $valCol);
        }, '=', count($valCol))->get();
        if (is_null($firmwareDB) || empty($firmwareDB)){
            return ['status' =>'error','message'=>'Device Not Found'];
        }
        $deviceDB = Device::select('device_id', 'device_token')->where('device_id', $deviceData['id_device'])->first();
        if (is_null($deviceDB)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        //make zip
        $tempZip = tempnam(sys_get_temp_dir(), "firmware.zip");
        $zip = new ZipArchive();
        $zip->open($tempZip, ZipArchive::CREATE);
        if($opt == 'single'){
            $firmwareDB = $firmwareDB[0] + $deviceDB;
            $composable($firmwareDB, $zip);
        }else if($opt == 'all'){
            foreach($firmwareDB as $item){
                $item += $deviceDB;
                $composable($item, $zip);
            }
        }
        $zip->close();
        return ['path' => $tempZip, 'name' => $deviceData['version'] . '.zip'];
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
        $fileContent = file_get_contents($file);
        $fileName = $file->hashName();
        Storage::disk('firmware')->put($request->input('device') . "/$fileName", Crypt::encrypt($fileContent));
        //add firmware
        $createFirmware = Firmware::insert([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $fileName,
            'checksum' => hash('sha512', $fileContent),
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
        if (!$request->hasFile('firmware')) {
            return response()->json(['status'=>'error','message'=>'Firmware Must Filled !'], 400);
        }
        $firmware = Firmware::select('file')->where('uuid', $request->input('id_firmware'))->where('device', $request->input('device'))->first();
        if (is_null($firmware)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
        }
        //process file
        $file = $request->file('firmware');
        if(!($file->isValid() && in_array($file->extension(), ['bin']))){
            return response()->json(['status'=>'error','message'=>'Invalid Format Firmware !'], 400);
        }
        $filePath = storage_path('app/firmware/'. $request->input('device') . '/' . $firmware['file']);
        if (file_exists($filePath) && !is_dir($filePath)) {
            unlink($filePath);
        }
        Storage::disk('firmware')->delete($request->input('device') . '/' . $firmware->file);
        $manifest = [
            'name' => $request->input('name'),
            'release_notes' => $request->input('version'),
            'device' => $request->input('device'),
            'version' => $request->input('version'),
            'release_date' => $request->input('version'),
        ];
        $fileContent = file_get_contents($file);
        $fileName = $file->hashName();
        Storage::disk('firmware')->put($request->input('device') . "/$fileName", Crypt::encrypt($fileContent));
        //update firmware
        $updateFirmware = $firmware->where('uuid', $request->input('id_firmware'))->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'version' => $request->input('version'),
            'release_date' => Carbon::createFromFormat('d-m-Y', $request->input('release_date'))->format('Y-m-d'),
            'file' => $request->input('version') . '.zip',
            'checksum' => $fileName,
            'device' => $request->input('device'),
            'updated_at' => Carbon::now(),
        ]);
        if (!$updateFirmware) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengubah data Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil diupdate']);
    }
    public function deleteFirmware(Request $request){
        $validator = Validator::make($request->only('id_firmware', 'device'), [
            'id_firmware' => 'required',
            'device' => 'required',
        ], [
            'id_firmware.required' => 'ID Firmware must filled !',
            'device.required' => 'Device must filled !',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        // if(!in_array($request->input('device'), self::$allDevice)){
        //     return response()->json(['status' => 'error', 'message' => 'Invalid name device'], 400);
        // }
        $firmware = Firmware::select('file', 'device')->where('uuid', $request->input('id_firmware'))->first();
        if (is_null($firmware)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
        }
        //delete file
        $fileToDelete = storage_path('app/firmware/'. $firmware['device'] . '/' . $firmware->file);
        if (file_exists($fileToDelete) && !is_dir($fileToDelete)) {
            unlink($fileToDelete);
        }
        Storage::disk('firmware')->delete($firmware['device'] . '/' . $firmware->file);
        if (!Firmware::where('uuid', $request->input('id_firmware'))->delete()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data Firmware'], 500);
        }
        return response()->json(['status'=>'error', 'message'=>'Data Firmware berhasil dihapus']);
    }
}