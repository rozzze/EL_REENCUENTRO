<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si el usuario no está logueado, o su rol no está en la lista de permitidos, lo botamos
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'No tienes permisos suficientes para acceder a este módulo.');
        }

        return $next($request);
    }
}
