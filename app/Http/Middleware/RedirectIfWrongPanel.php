<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfWrongPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $path = trim($request->path(), '/');

        // Rediriger les prestataires et clients qui tentent d'accéder au panel admin
        // Utilisons le préfixe configuré dans AdminPanelProvider
        if (str_starts_with($path, 'up-fiesta-kygj')) {
            if ($user->role === 'provider') {
                return redirect('/prestataire');
            }

            if ($user->role === 'client') {
                return redirect()->route('home');
            }
        }

        // Rediriger les clients et admins qui tentent d'accéder au panel prestataire
        if (str_starts_with($path, 'prestataire')) {
            if ($user->role === 'client') {
                return redirect()->route('home');
            }

            if ($user->role === 'admin') {
                return redirect('/up-fiesta-kygj');
            }
        }

        return $next($request);
    }
}

