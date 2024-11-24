<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use Illuminate\Http\Request;
use App\Models\Firmware;
use App\Models\Device;
class PublicController extends Controller
{
    public function showHome(Request $request){
        $allDevice = Device::select('foto')->where('uuid',$request->input('uuid'))->first();
        if (is_null($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        $dataShow = [
            'viewData' => $allDevice,
        ];
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
    public function showFirmware(Request $request){
        $allDevice = Device::select('foto')->where('uuid',$request->input('uuid'))->first();
        if (is_null($allDevice)) {
            return response()->json(['status' =>'error','message'=>'Device Not Found'], 400);
        }
        $dataShow = [
            'viewData' => $allDevice,
        ];
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
}