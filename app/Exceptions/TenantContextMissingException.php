<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class TenantContextMissingException extends Exception
{
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tenant context is missing. Please refresh the page and try again.',
            ], 500);
        }

        abort(500, 'Tenant context is missing. Please refresh the page and try again.');
    }
}
