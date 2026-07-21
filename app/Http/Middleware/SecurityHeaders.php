<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and attach HTTP security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self' https: http: data: blob: 'unsafe-inline' 'unsafe-eval'; script-src 'self' https: http: data: blob: 'unsafe-inline' 'unsafe-eval'; script-src-elem 'self' https: http: data: blob: 'unsafe-inline' 'unsafe-eval'; script-src-attr 'self' 'unsafe-inline'; style-src 'self' https: http: 'unsafe-inline'; img-src * data: blob:; frame-src *; connect-src *; frame-ancestors 'self' https://*.google.com https://*.doubleclick.net https://*.google.adservices.com https://*.googlesyndication.com;"
        );
        
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        return $response;
    }
}
