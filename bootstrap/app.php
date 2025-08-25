<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register aliases here (no Kernel.php in Laravel 12)
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // If you ever need to add to groups:
        // $middleware->web(append: [ ... ]);
        // $middleware->api(append: [ ... ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
