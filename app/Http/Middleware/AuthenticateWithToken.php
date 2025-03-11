<?php

namespace App\Http\Middleware;

use App\Models\AccountModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token || !($user = AccountModel::where('api_token', $token)->first())) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        auth()->setUser($user);
        return $next($request);
    }
}
