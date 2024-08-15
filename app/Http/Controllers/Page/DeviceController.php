<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Device;
use Inertia\Inertia;
class DeviceController extends Controller
{
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
    public function showDevice(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Device::select('uuid', 'nama_device', 'device_id')->where('device.id_user', $userAuth['id_user'])->join('users', 'device.id_user', '=', 'users.id_user')->limit(10)->get();
        if (empty($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Device Empty'], 400);
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
    public function detailDevice(Request $request, $link){
        $userAuth = $request->input('user_auth');
        $allDevice = Device::select('uuid', 'nama_device', 'device_id', 'device_token', 'activated')->where('device.uuid', $link)->where('users.id_user', $userAuth['id_user'])->join('users', 'device.id_user', '=', 'users.id_user')->first();
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
}