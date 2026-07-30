<?php

namespace App\Http\Middleware\Tenancy;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadTenantConfiguration
{
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'app.url' => $request->getSchemeAndHttpHost(),
        ]);

        return $next($request);
    }
}
