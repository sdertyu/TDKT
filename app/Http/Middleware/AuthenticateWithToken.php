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
        $hashToken = hash('sha256', $token);
        // Log:info('Token: ' . $hashToken);

        if (!$hashToken || !($user = AccountModel::where('api_token', $hashToken)->first())) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        auth()->setUser($user);
        return $next($request);
    }
}
