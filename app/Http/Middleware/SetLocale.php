<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('Upfiesta-kygj/login')) {
            app()->setLocale(config('app.locale'));
            return $next($request);
        }

        $locale = session('locale');

        if (!is_string($locale) || $locale === '') {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        
        // Debugging
        // logger('Locale set to: ' . app()->getLocale());

        return $next($request);
    }
}



