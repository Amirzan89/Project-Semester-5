<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\JwtController;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Exception;
class ChangePasswordController extends Controller
{
    public function showCreatePass(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'nama'=>'required',
        ],[
            'nama.required'=>'nama wajib di isi',
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors = $errorMessages[0];
            }
            return response()->json(['status' => 'error', 'message' => $errors], 400);
            // return response()->json(['status'=>'error','message'=>$validator->failed()],400);
        }
        $email = $request->input('email');
        $nama = $request->input('nama');
        // $costum = new Request();
        // $costum->replace(['email'=>$decoded['data'][0][0]['email'],'mode'=>'count']);
        // $total = $deviceController->getDevice($costum,$device)->getData();
        // return view('page.dashboard',['total'=>$total->data, 'penuh'=>0, 'kosong'=>0,'email'=>$decoded['data'][0][0]['email'],'nama'=>$decoded['data'][0][0]['nama'],'number'=>$number]);
        return view('page.forgotPassword',['email'=>$email,'nama'=>$nama,'div'=>'register','title'=>'Reset Password','description'=>'changePass','code'=>'','link'=>'']);
    }
    //register user using google
    public function showVerify(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'nama'=>'required',
        ],[
            'nama.required'=>'nama wajib di isi',
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors = $errorMessages[0];
            }
            return response()->json(['status' => 'error', 'message' => $errors], 400);
            // return response()->json(['status'=>'error','message'=>$validator->failed()],400);
        }
        $email = $request->input('email');
        $nama = $request->input('nama');
        return view('page.forgotPassword',['email'=>$email,'nama'=>$nama,'div'=>'verifyDiv','title'=>'Register Google','description'=>'createUser','code'=>'','link'=>'']);
    }
}
?>