<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Filament ima prevode u lang/sr_Latn, a naši tekstovi su u lang/sr.
 * U admin panelu uključujemo sr_Latn; ključevi koji nedostaju (admin.*,
 * validation.*) dolaze iz rezervnog jezika sr.
 */
class AdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('sr_Latn');

        return $next($request);
    }
}
