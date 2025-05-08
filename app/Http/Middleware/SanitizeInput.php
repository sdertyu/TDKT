<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    // Ký tự nguy hiểm cần chặn
    protected $forbiddenCharacters = ['<', '>', '‘', '“', '%'];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($this->forbiddenCharacters as $char) {
                    if (strpos($value, $char) !== false) {
                        return response()->json([
                            'errors' => ["Trường chứa ký tự không hợp lệ."],
                        ], 422);
                    }
                }
            }
        }

        return $next($request);
    }
}
