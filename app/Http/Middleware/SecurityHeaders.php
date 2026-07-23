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

        // Remove X-Frame-Options to allow Google AdSense site preview tool to frame pages
        $response->headers->remove('X-Frame-Options');

        $adsenseEnabled = false;
        try {
            $adsenseEnabled = (bool) \App\Models\Setting::get('adsense_enabled', false);
        } catch (\Throwable $e) {}

        if (!$adsenseEnabled) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self' https: data: blob: 'unsafe-inline' 'unsafe-eval'; frame-ancestors 'self';"
            );
        } else {
            // Omit CSP header when AdSense is enabled so ad scripts, eval, and previews are not blocked
            $response->headers->remove('Content-Security-Policy');
        }

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        return $response;
    }
}
