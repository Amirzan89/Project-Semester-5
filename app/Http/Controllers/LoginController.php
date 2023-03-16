<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
class LoginController extends Controller
{
    public function Login(Request $request,Response $response){
        echo "asuuu<br>";
        // return redirect('/dashboard');
        $email = $request->input("email");
        $email = "Admin@gmail.com";
        $pass = $request->input("pass");
        $pass = "Admin@123456789";
        if(empty($email)){
            return redirect('/login',302,['error'=>'Email tidak boleh kosong']);
        }else if(empty($pass)){
            return redirect('/login',302,['error'=>'Password tidak boleh kosong']);
        }else{
            $inEmail = ""; $rPass = "";
            $inId = User::select('username')->where('email','=',$email)->limit(1)->get();
            $inEmail = User::select('email')->where('email','=',$email)->limit(1)->get();
            $inPass = User::select('password')->where('email','=',$email)->limit(1)->get();
            $id = json_decode(json_encode($inId));
            $Iemail = json_decode(json_encode($inEmail));
            $Ipass = json_decode(json_encode($inPass));
            // echo var_dump($id);
            // echo "<br>";
            // echo var_dump($Iemail);
            // echo "<br>";
            // echo var_dump($Ipass);
            // echo "<br>";
            if(!$email == $Iemail){
                echo $inEmail."<br>";
                echo "email salah";
                // return redirect("/login",302,['error'=>'Email Salah']);
            }else if(!password_verify($pass,$Ipass[0]->password)){
                // echo "pass ".Hash::make($pass)."<br>";
                echo "".password_hash($pass,PASSWORD_BCRYPT)."<br>";
                echo $pass. "<br>";
                // echo $Ipass[0]->password. "<br>";
                echo "pass salah";
                // return redirect("/login",302,['error'=>'Password Salah']);
            }else{
                echo "email dan password benar<br>";
                $waktu = time() + (60 * 60 * 24 * 1);
                echo "waktu $waktu<br>";
                return redirect("/dashboard")->withCookies([cookie('id',$id[0]->username,$waktu),cookie('key',hash("sha512",$email),$waktu)]);
            }
        }
    }
}
?>