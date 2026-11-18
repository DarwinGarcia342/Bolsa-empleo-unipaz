<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveApiUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->active) {
            return response()->json([
                'message' => 'Tu cuenta está inactiva.',
            ], 403);
        }

        return $next($request);
    }
}
