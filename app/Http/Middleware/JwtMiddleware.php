<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Cek apakah token ada
            if ($token = JWTAuth::getToken()) {
                // Parse token dan autentikasi pengguna
                $user = JWTAuth::parseToken()->authenticate();
                $request->merge(['member_id' => $user->member_id]);
            }
        } catch (JWTException $e) {
            // Token tidak valid atau tidak ada
            // Jangan ubah request member_id jika token tidak ada
        }

        return $next($request);
    }
}
