<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TblSessions;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado o formato inválido'
            ], 401);
        }

        $token = trim(str_replace('Bearer ', '', $header));
        $session = TblSessions::where('token', hash('sha256', $token))->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o sesión no existe'
            ], 401);
        }

        $request->merge([
            'id_users_auth' => $session->id_users
        ]);

        return $next($request);
    }
}