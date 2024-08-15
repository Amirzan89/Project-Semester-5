<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\FirmwareController as ServiceFirmware;
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
    public function showFirmware(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Firmware::select('uuid', 'version', 'device',)->limit(10)->get();
        if (empty($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Firmware Empty'], 400);
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
    public function showTambah(Request $request){
        $userAuth = $request->input('user_auth');
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
        ];
        if ($request->wantsJson()) {
            return response()->json($dataShow);
        }
        return $this->getView();
    }
    public function detailFirmware(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Firmware::select('uuid', 'name', 'device', '')->where('uuid',$request->input('uuid'))->first();
        if (is_null($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Firmware Not Found'], 400);
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
    public function downloadFirmware(Request $request){
        $validator = Validator::make($request->only('id_device', 'device'), [
            'id_device' => 'required',
            'device' => 'required',
        ], [
            'id_device.required' => 'ID Device must filled !',
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
        if(!in_array($request->input('device')['name'], self::$allDevice)){
            return response()->json(['status' => 'error', 'message' => 'Invalid name device'], 400);
        }
        $resultZip = ServiceFirmware::setZip(['id_device'=>$request->input('id_device'), 'device'=>$request->input('device')], 'all');
        return Response::download($resultZip['path'], $resultZip['name'])->deleteFileAfterSend(true);
    }
}