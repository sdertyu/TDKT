<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccountModel;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $token = $request->bearerToken();
        if (!$token || !($user = AccountModel::where('api_token', $token)->first())) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        $user = AccountModel::where('api_token', $token)->first();

        if (in_array($user->FK_MaQuyen, $permissions)) {
            return $next($request);
        }

        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);

        // return $next($request);
    }
}
