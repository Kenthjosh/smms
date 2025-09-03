<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add your custom middleware aliases
        $middleware->alias([
            'scholarship.scopes' => \App\Http\Middleware\ApplyScholarshipScopes::class,
            'committee.role' => \App\Http\Middleware\EnsureCommitteeRole::class,
            'student.role' => \App\Http\Middleware\EnsureStudentRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
