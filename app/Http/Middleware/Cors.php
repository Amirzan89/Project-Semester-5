<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Closure;
class Cors
{
    public function handle(Request $request, Closure $next){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        return $next($request);
    }
}