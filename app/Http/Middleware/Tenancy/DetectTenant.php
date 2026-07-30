<?php

namespace App\Http\Middleware\Tenancy;

use App\Models\Tenancy\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHttpHost();
        $tenant = Tenant::where('subdomain', $host)
            ->orWhere('domain', $host)
            ->first();

        if (!$tenant) {
            return redirect()->route('unknownTenant');
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
