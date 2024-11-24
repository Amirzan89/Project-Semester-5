<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use Illuminate\Http\Request;
use App\Models\Device;
class DeviceController extends Controller
{
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
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
    public function showTambah(Request $request){
        $userAuth = $request->input('user_auth');
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
        ];
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
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
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
}