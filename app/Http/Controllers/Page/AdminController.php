<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use Illuminate\Http\Request;
use App\Models\User;
class AdminController extends Controller
{
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
        return UtilityController::getView('', $dataShow, $request->wantsJson() ? 'json' : ['cond' => ['view', 'redirect'], 'redirect' => '/' . $request->path()]);
    }
}