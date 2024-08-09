<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Firmware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
class FirmwareController extends Controller
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
    public function allFirmware(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = Firmware::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
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
        $allDevice = Firmware::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
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
}