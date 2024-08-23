<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
class RecaptchaController extends Controller
{
    public function verify($token){
        if(empty($token) || is_null($token)){
            return ['status'=>'error','message'=>'token empty'];
        }
        $response = Http::post('https://www.google.com/recaptcha/api/siteverify?secret=' . env('RECAPTCHA_SITE_SECRET') . '&' . "response=$token")->json();
        if(!$response['success']){
            return ['status' => 'error', 'message' => $response['error-codes']];
        }
        if($response["success"] == true && ! empty($response["action"]) && $response["score"] >= 0.5){
            return ['status' => 'success', 'message' => 'success verify reCaptcha'];
        } else {
            return ['status' => 'error', 'message' => 'error verify reCaptcha'];
        }
    }
}