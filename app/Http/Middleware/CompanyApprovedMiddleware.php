<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyApprovedMiddleware
{
    /**
     * Verifica que la empresa esté aprobada por UNIPAZ antes de publicar vacantes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user    = Auth::user();
        $company = $user->company;

        if (!$company || !$company->isApproved()) {
            return redirect()->route('company.dashboard')
                ->with('warning', 'Tu empresa aún está pendiente de aprobación por UNIPAZ. Podrás publicar vacantes una vez sea verificada.');
        }

        return $next($request);
    }
}
