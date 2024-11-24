<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Closure;
class Authorization
{
    // private $roleAdmin = ['admin'];
    public function handle(Request $request, Closure $next){
        $userAuth = $request->input('user_auth');
        $path = '/'.$request->path();
        //only admin can access admin feature
        if(in_array($userAuth['role'], ['user']) && !Str::startsWith($path, ['/api/mobile'])){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'], 403);
        }
        //only super admin can access /admin
        if(in_array($userAuth['role'],['admin disi','admin emotal','admin pengasuhan', 'admin nutrisi', 'user']) && Str::startsWith($path, '/admin')){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'], 403);
        }
        //only super admin and admin disi can access /disi
        if(in_array($userAuth['role'],['admin emotal','admin nutrisi', 'admin pengasuhan','user']) && Str::startsWith($path, '/disi')){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'], 403);
        }
        return $next($request);
    }
}