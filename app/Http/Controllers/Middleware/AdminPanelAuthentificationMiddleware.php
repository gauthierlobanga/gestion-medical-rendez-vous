<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPanelAuthentificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()) {
            return redirect()->route('medical.accueil')
                ->with('error', 'Votre compte n\'a pas de profil médecin associé. Veuillez contacter l\'administrateur.');
        }

        if (Auth::user() && Auth::user()->hasRole('Super Admin')) {
            return $next($request);
        }

        return redirect('/');
    }
}
