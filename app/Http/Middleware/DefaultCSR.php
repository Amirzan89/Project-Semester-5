<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Closure;
class DefaultCSR
{
    private static $exceptPage = ['/sanctum/csrf-cookie', '/auth/redirect', '/auth/google', '/auth/google-tap', '/verify/email', '/verify/password'];
    public function handle(Request $request, Closure $next){
        $path = '/' . ltrim($request->path(), '/');
        foreach (self::$exceptPage as $exceptPath) {
            if (strpos($path, $exceptPath) === 0) {
                return $next($request);
            }
        }
        if ($request->header('Accept') !== 'application/json' && !in_array($path, self::$exceptPage)) {
            return response()->file(public_path() . '/index.html');
        }else if($request->isMethod('get') && $request->header('Accept') !== 'application/json'){
            return $next($request)->header('Cache-Control', 'no-cache, no-store, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', '0');
        }
        return $next($request);
    }
}