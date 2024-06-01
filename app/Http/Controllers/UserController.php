<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
class UserController extends Controller
{
    public function getDefaultFoto(Request $request){
        $referrer = $request->headers->get('referer');
        if (!$referrer && $request->path() == 'public/download/foto') {
            abort(404);
        }
        return response()->download(storage_path('app/' . 'admin/default.jpg'), 'foto.' . pathinfo('admin/default.jpg', PATHINFO_EXTENSION));
    }
    public function getFotoProfile(Request $request){
        $userAuth = $request->input('user_auth');
        $referrer = $request->headers->get('referer');
        if (!$referrer && $request->path() == 'public/download/foto') {
            abort(404);
        }
        if (empty($userAuth['foto']) || is_null($userAuth['foto'])) {
            $defaultPhotoPath = 'admin/default.jpg';
            return response()->download(storage_path('app/' . $defaultPhotoPath), 'foto.' . pathinfo($defaultPhotoPath, PATHINFO_EXTENSION));
        } else {
            $filePath = storage_path('app/admin/foto/' . $userAuth['foto']);
            if (!empty($userAuth['foto'] && !is_null($userAuth['foto'])) && file_exists($filePath) && is_file($filePath)) {
                return response(Crypt::decrypt(file_get_contents($filePath)));
            }
            abort(404);
        }
    }
    public function fetchData(Request $request){
        return response()->json(['']);
    }
}