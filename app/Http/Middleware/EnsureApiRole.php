<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user?->active) {
            return response()->json(['message' => 'Tu cuenta está inactiva.'], 403);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json(['message' => 'No tienes permisos para esta operación.'], 403);
        }

        return $next($request);
    }
}
