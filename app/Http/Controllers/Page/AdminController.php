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
    public function showAdmin(Request $request){
        $userAuth = $request->input('user_auth');
        $adminData = User::select('uuid', 'nama_lengkap', 'email', 'role')->limit(10)->get();
        if(empty($adminData)){
            return response()->json(['status' =>'error','message'=>'Admin Empty'], 400);
        }
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
            'viewData' => $adminData,
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
    public function detailAdmin(Request $request, $link){
        $userAuth = $request->input('user_auth');
        $adminDetail = User::select('uuid', 'nama_lengkap', 'email', 'role', 'foto')->where('uuid', $link)->first();
        if(is_null($adminDetail)){
            return response()->json(['status' =>'error','message'=>'Detail Admin Not Found'], 400);
        }
        unset($userAuth['id_user']);
        $dataShow = [
            'userAuth' => $userAuth,
            'viewData' => $adminDetail,
        ];
        if ($request->wantsJson()) {
            return response()->json($dataShow);
        }
        return $this->getView();
    }
}