<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ShareholderMiddleware;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\RequirePermission;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', EnsureUserIsActive::class);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'staff' => StaffMiddleware::class,
            'permission' => RequirePermission::class,
            'shareholder' => ShareholderMiddleware::class,
        ]);

        $middleware->prependToPriorityList(
            SubstituteBindings::class,
            RequirePermission::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
