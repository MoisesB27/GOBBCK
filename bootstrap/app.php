<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Importa tu middleware personalizado
use App\Http\Middleware\OptionalAuthenticate;

//  Importar el middleware de Spatie para roles
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Agregar middleware globalmente (se ejecuta en todas las peticiones)
        // $middleware->append(OptionalAuthenticate::class);

        // O registrar un alias (para usar en rutas con middleware('optional.auth'))
        $middleware->alias([
            'optional.auth' => OptionalAuthenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
