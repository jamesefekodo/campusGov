<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\Tenancy\DetectTenant;
use App\Http\Middleware\Tenancy\LoadTenantConfiguration;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $tenancy = DetectTenant::class;
        $tenancyEnvConfig = LoadTenantConfiguration::class;
        $authMiddleware = AuthenticationMiddleware::class;
        $adminMiddleware = AdminMiddleware::class;
        $studentMiddleware = StudentMiddleware::class;
        $roleMiddleware = RoleMiddleware::class;
        $permissionMiddleware = PermissionMiddleware::class;

         // Initialize tenant before session and authentication processing.
    $middleware->web(prepend: [
        $tenancy,
    ]);
    

        // Authentication
        $middleware->appendToGroup('authenticationMiddlewares', [
            $tenancy,
            $authMiddleware,
            $tenancyEnvConfig,
        ]);

        // Admin
        $middleware->appendToGroup('adminMiddlewares', [
            $adminMiddleware,
            $permissionMiddleware,
            $roleMiddleware,
        ]);

        // student
        $middleware->appendToGroup('studentMiddlewares', [
            $studentMiddleware,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );
    })->create();
