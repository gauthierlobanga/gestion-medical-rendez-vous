<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMedecinProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->medecin && $user->hasRole(['medecin', 'Medecin Chef Service'])) {
            return redirect()->route('medecin.dashboard')
                ->with('error', 'Votre compte n\'a pas de profil médecin associé. Veuillez contacter l\'administrateur.');
        }

        return $next($request);
    }
}
