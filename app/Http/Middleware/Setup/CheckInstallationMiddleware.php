<?php

namespace App\Http\Middleware\Setup;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;


class CheckInstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!File::exists(storage_path('installed'))) {
            return redirect()->route('installSystem');
        }

        return $next($request);
    }
}
