<?php
namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use App\Models\User;
Use Illuminate\Routing\UrlGenerator;
use Illuminate\Console\Command;
class Authenticate
{
    protected $response;
    public function __construct(Response $response){
        $this->response = $response;
    }
    public function handle(Request $request, Closure $next){
        // $previousUrl = $request->header('referer');
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $pathh = $request->path();
        echo "dahlahh  $pathh <br>";
        $previousUrl = url()->previous();
        $path = parse_url($previousUrl, PHP_URL_PATH);
        $path = ltrim($path, '/');
        $page = ['dashboard','digital','pengaturan','analog'];
        if($request->hasCookie("id") && $request->hasCookie("key")){
            echo "coookie ada <br>";
            if(($request->path() === 'login' || 'register') && $request->isMethod("get")){
                echo "coookie11 ada <br>";
                $c_id = $request ->cookie("id");
                $c_key = $request ->cookie("key");
                // echo "id $c_id<br>";
                // echo "key $c_key<br>";
                if(!User::select("email")->where('username','=',$c_id)->limit(1)->exists()){
                    // echo "cookie username salah";
                    return redirect("/login")->withCookies([cookie('id','',time()-3600),cookie('key','',time()-3600)]);
                    // return redirect('/login');
                }else{
                    $id1 = json_decode(json_encode(DB::table("users")->select("email")->where('username','=', $c_id)->limit(1)->get()));
                    echo $id1[0]->email."<br>";
                    if(!hash_equals($c_key, hash("sha512",$id1[0]->email))){
                        echo "cookie key salah";
                        return redirect("/login")->withCookies([cookie('id','',time()-3600),cookie('key','',time()-3600)]);
                    }else{
                        echo "cookie key benar <br>";
                        //digunakan untuk redirect
                        $page = ['login','register'];
                        if(in_array($request->path(),$page)){
                            return redirect('/dashboard');
                        }
                        // if($request->path() === '/dashboard' && $request->isMethod('get')){
                        //     if($path == 'dashboard'){
                        //     }else{
                        //         return redirect('/dashboard');
                        //     }
                        // }
                        //digunkan untuk menangani looping redirect
                        if($path == 'login'){
                            if($request->path === '/login' && $request->isMethod('get')){
                                // return back()->with('error', 'You cannot access this page.');
                            }
                        }
                        //untuk login
                        if(($request->path() === 'login-form' || 'register-form') && $request->isMethod('post')){
                            if($path === 'dashboard'){
                                return back()->with('error', 'You cannot access this page.');
                            }else{
                                return redirect('/dashboard');
                            }
                        }
                        if(!in_array('login' || 'register',$page)){
                            //
                        }
                    }
                }
            }
            if($request -> path() === 'logout' && $request->isMethod('post')){
                echo "logouut <br>";
                return $this->logout($request, $next);
            }
        }else{
            echo "cookie hilangg<br>";
            // var_dump($request->hasCookie('id'));
            // var_dump($request->hasCookie('key'));
            // $previousUrl = $request->header('referer');
            if($request->hasCookie("id") || $request->hasCookie("key")){
                $this->response->withCookie(cookie()->forget('id'));
                $this->response->withCookie(cookie()->forget('key'));
                // echo "kosongg <br>";
                $pathh = $request->path();
                echo "patthh $pathh  <br>";
                $page = ['dashboard','digital','pengaturan','analog'];
                if(in_array($request->path(),$page)){
                    return redirect('/login');
                }
                $previousUrl = url()->previous();
                echo "sebelumnya $previousUrl<br>";
                $path = parse_url($previousUrl, PHP_URL_PATH);
                $path = ltrim($path, '/');
                echo "patthhh  $path<br>";
                //digunakan untuk menangani looping redirect
                if($path == 'login'){
                    if($request->path === '/login' && $request->isMethod('get')){
                        // return back()->with('error', 'You cannot access this page.');
                    }
                }
            }
        }
        return $next($request);
    }
    protected function logout($request,Closure $next){
        return redirect('/login')->withCookies([Cookie::forget('id'),Cookie::forget('key')]);
    }
}
