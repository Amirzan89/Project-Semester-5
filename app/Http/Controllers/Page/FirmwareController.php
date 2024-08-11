<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\Firmware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
class FirmwareController extends Controller
{
    private static $allDevice;
    public function __construct(){
        self::$allDevice = ['arduino', 'esp32'];
    }
    private function getView($name = null, $dataShow = null){
        $env = env('APP_VIEW', 'blade');
        if($env == 'blade'){
            return view($name);
        }else if($env == 'inertia'){
            return Inertia::render('app', $dataShow);
        }else if($env == 'nuxt'){
            $indexPath = public_path('dist/index.html');
            if (File::exists($indexPath)) {
                $htmlContent = File::get($indexPath);
                $htmlContent = str_replace('<body>', '<body>' . '<script>const csrfToken = "' . csrf_token() . '";</script>', $htmlContent);
                return response($htmlContent)->cookie('XSRF-TOKEN', csrf_token(), 0, '/', null, false, true);
            } else {
                return response()->json(['error' => 'Page not found'], 404);
            }
        }
    }
    public function allFirmware(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Firmware::select('foto')->where('uuid',$request->input('uuid'))->first();
        if (is_null($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
            'viewData' => $allDevice,
        ];
        if ($request->wantsJson()) {
            return response()->json($dataShow);
        }
        return $this->getView();
    }
    public function detailFirmware(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Firmware::select('foto')->where('uuid',$request->input('uuid'))->first();
        if (is_null($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
            'viewData' => $allDevice,
        ];
        $update = Firmware::where('version', '>', $request->input('current_version'))->first();
        if ($request->wantsJson()) {
            return response()->json($dataShow);
        }
        return $this->getView();
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