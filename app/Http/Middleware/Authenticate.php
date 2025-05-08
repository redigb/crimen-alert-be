<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Verifica si viene Authorization Bearer token
         $authHeader = $request->header('Authorization');

         if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'No autenticado.'
            ], 401);
         }
         // Continúa si el token existe (lo validará sanctum)
         return $next($request);
    }
}
