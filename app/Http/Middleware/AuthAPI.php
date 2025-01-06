<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Fetch allowed domains from environment variable as a comma-separated string
        $allowedDomains = explode(',', env('ALLOWED_DOMAINS', ''));

        // Get the Referer and Origin headers
        $referer = $request->headers->get('referer');
        $origin = $request->headers->get('origin');

        // Check if any allowed domain is part of the Referer or matches the Origin
        $isRefererAllowed = $referer && collect($allowedDomains)->contains(function ($domain) use ($referer) {
            return str_contains($referer, trim($domain));
        });

        $isOriginAllowed = $origin && collect($allowedDomains)->contains(function ($domain) use ($origin) {
            return $origin === trim($domain);
        });

        if (!$isRefererAllowed && !$isOriginAllowed) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
