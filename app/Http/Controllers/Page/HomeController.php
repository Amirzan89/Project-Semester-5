<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
class HomeController extends Controller
{
    public function showDashboard(Request $request){
        $userAuth = $request->input('user_auth');
        // $allDevice = Device::select('foto')->where('uuid',$request->input('uuid'))->first();
        // if (is_null($allDevice)) {
        //     return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        // }
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
            // 'viewData' => $allDevice,
        ];
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
    public function showProfile(Request $request){
        $userAuth = $request->input('user_auth');
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
        ];
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
}