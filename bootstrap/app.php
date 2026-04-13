<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // AJOUT DE CETTE LIGNE
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Désactiver CSRF pour l'API et s'assurer que le CORS est géré
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->statefulApi(); // Important pour Sanctum/CORS
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
