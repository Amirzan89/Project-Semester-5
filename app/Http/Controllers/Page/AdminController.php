<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
class HomeController extends Controller
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
    public function getHome(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = User::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
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
    public function fetchData(Request $request){
        $userAuth = $request->input('user_auth');
        $allDevice = User::select('foto')->where('uuid',$request->input('uuid'))->limit(1)->first();
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