<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class VerifyApiCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // API webhooks (payment providers, etc.)
        'api/webhooks/*',
        'api/paygate/*',
        'api/notifications/*',
        
        // External integrations
        'api/integrations/*',
        
        // Public endpoints
        'api/public/*',
        'api/search/*',
        
        // OAuth and authentication flows
        'oauth/*',
        'login',
        'register',
        'password/*',
        
        // File uploads (handled separately)
        'api/upload/*',
        
        // Health checks
        'api/health',
        'api/status',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // Skip CSRF for API routes with proper authentication
        if ($this->isApiRequest($request) && $this->hasValidApiToken($request)) {
            return $next($request);
        }

        // Enhanced CSRF validation for AJAX requests
        if ($this->isAjaxRequest($request) && !$this->tokensMatch($request)) {
            Log::warning('CSRF token mismatch detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'error' => 'csrf_mismatch',
                    'code' => 419
                ], 419);
            }

            throw new TokenMismatchException('CSRF token mismatch.');
        }

        return parent::handle($request, $next);
    }

    /**
     * Determine if the request is an API request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/') ||
               $request->expectsJson() ||
               $request->is('api/*');
    }

    /**
     * Determine if the request has a valid API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasValidApiToken(Request $request): bool
    {
        // Check for Bearer token
        if ($request->bearerToken()) {
            return true;
        }

        // Check for API key in header
        if ($request->header('X-API-Key')) {
            return true;
        }

        // Check for personal access token
        if ($request->user() && $request->user()->token()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the request is an AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() ||
               $request->expectsJson() ||
               $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Add CSRF token to response headers for API requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $response = parent::addCookieToResponse($request, $response);

        // Add CSRF token to headers for API requests
        if ($this->isApiRequest($request)) {
            $response->headers->set('X-CSRF-TOKEN', $request->session()->token());
        }

        return $response;
    }

    /**
     * Determine if the cookie should be set on the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldAddXsrfTokenCookieToResponse($request)
    {
        // Don't set CSRF cookie for API requests with valid tokens
        if ($this->isApiRequest($request) && $this->hasValidApiToken($request)) {
            return false;
        }

        return parent::shouldAddXsrfTokenCookieToResponse($request);
    }
}
