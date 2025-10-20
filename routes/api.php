<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLADORES UTILIZADOS ---
// Asegúrate de que todos los controladores necesarios estén importados.
use App\Http\Controllers\Api\ActividadeController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\FormularioController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\InstitucionesController;
use App\Http\Controllers\Api\InstitutionContactController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\PgobController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SoporteController;
use App\Http\Controllers\Api\TestimonioController;
use App\Http\Controllers\Api\TramiteController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppointmentServiceController;
use App\Http\Controllers\Api\AppointmentAccessLogController;
use App\Http\Controllers\Api\ServiceStatusesController;
use App\Http\Controllers\Api\TicketStatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\PreferenciaController;

// Usamos la FQCN del middleware de Spatie para evitar fallos en la carga
use Spatie\Permission\Middleware\RoleMiddleware;

// ====================================================================
// 1. RUTAS PÚBLICAS (Sin autenticación)
// ====================================================================

// Autenticación
Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

// Rutas de información pública/lectura sin seguridad
Route::apiResource('ubicaciones', UbicacionController::class)->only(['index', 'show']);
Route::apiResource('testimonios', TestimonioController::class)->only(['index', 'show']);

// ====================================================================
// 2. RUTAS AUTENTICADAS (Requieren inicio de sesión: 'auth:sanctum')
//    Estas rutas aplican a todos los usuarios logueados (clientes y administradores)
// ====================================================================
Route::middleware('auth:sanctum')->group(function () {

    // Autenticación y Perfil
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('change-password', [LoginController::class, 'changePassword']);


    // GESTIÓN DE PERFIL DEL USUARIO (GET/PUT/PATCH /api/profile)
    // Se usa 'apiResource' con 'only' para manejar show (GET) y update (PUT/PATCH) en el controlador.
    Route::apiResource('profile', ProfileController::class)->only(['show', 'update']);

    // RUTAS DE USUARIO COMÚN (Cliente)
    // La seguridad fina (ej: solo ver sus propios tickets) se gestiona DENTRO del controlador.
    Route::post('testimonios', [TestimonioController::class, 'store'])->middleware('can:create testimonios');

    // Módulos transaccionales y de usuario
    Route::apiResource('historiales', HistorialController::class)->only(['index', 'show', 'store', 'destroy']);

    // El cliente solo puede CREAR tickets, el index/show/update se aplica a su contexto.
    Route::apiResource('soportes', SoporteController::class)->only(['index', 'show', 'store']);

    // El cliente puede ver, crear, y actualizar (cancelar/modificar) sus citas.
    Route::apiResource('formularios', FormularioController::class)->only(['index', 'show', 'store', 'update']);
    Route::apiResource('appointments', AppointmentsController::class)->only(['index', 'show', 'store', 'update']);

    // Rutas de data personal del usuario
    Route::apiResource('notificaciones', NotificacionController::class)->only(['index', 'show', 'store']);
    Route::apiResource('preferencias', PreferenciaController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Rutas de consulta pública que usan el token para contexto (se mantienen aquí)
    Route::apiResource('pgobs', PgobController::class)->only(['index', 'show']);
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
    Route::apiResource('tramites', TramiteController::class)->only(['index', 'show']);
    Route::apiResource('instituciones', InstitucionesController::class)->only(['index', 'show']);


    // ====================================================================
    // 3. RUTAS DE ADMINISTRACIÓN OPERACIONAL (ROL: ADMIN)
    //    Requieren el rol 'admin' (delegado)
    // ====================================================================

    // Definimos el middleware de roles para el delegado
    $adminMiddleware = RoleMiddleware::class . ':admin';

    Route::middleware($adminMiddleware)->group(function () {

        // GESTIÓN DE SOPORTE (El delegado puede ver y actualizar tickets)
        Route::apiResource('admin/soportes', SoporteController::class)->only(['index', 'show', 'update']);

        // GESTIÓN DE CITAS (El delegado puede ver y actualizar citas de su PGOBy)
        Route::apiResource('admin/appointments', AppointmentsController::class)->only(['index', 'show', 'update']);

    }); // Fin del grupo role:admin


    // ====================================================================
    // 4. RUTAS DE ADMINISTRACIÓN GLOBAL (ROL: SUPER ADMIN)
    //    Requieren el rol 'superadmin' para todas las operaciones (store, update, destroy)
    // ====================================================================

    // Definimos el middleware de roles con la sintaxis robusta de FQCN
    $superAdminMiddleware = RoleMiddleware::class . ':superadmin';

    Route::middleware($superAdminMiddleware)->group(function () {

        // GESTIÓN DE USUARIOS (CRUD completo y asegurado)
        Route::apiResource('users', UserController::class); // Ya que el 'index' y 'show' de users es solo para admins

        // GESTIÓN DE CONTENIDO (CRUD completo y asegurado)
        Route::apiResource('actividades', ActividadeController::class);
        Route::apiResource('documentos', DocumentoController::class);
        Route::apiResource('formularios', FormularioController::class);
        Route::apiResource('testimonios', TestimonioController::class)->except(['index', 'show', 'store']); // Index/Show/Store públicos
        Route::apiResource('ubicaciones', UbicacionController::class)->except(['index', 'show']); // Index/Show públicos

        // GESTIÓN DE ESTRUCTURA Y DATA MAESTRA (CRUD completo y asegurado)
        Route::apiResource('pgobs', PgobController::class);
        Route::apiResource('services', ServiceController::class);
        Route::post('instituciones/{id}/tramites', [TramiteController::class, 'storeByInstitucion']);
        Route::apiResource('tramites', TramiteController::class);
        Route::apiResource('instituciones', InstitucionesController::class);

        // Rutas Anidadas y Tablas de PIVOTE/LOGS
        Route::apiResource('appointment-services', AppointmentServiceController::class);
        Route::apiResource('appointment-access-logs', AppointmentAccessLogController::class);

        // CONTROLADORES DE DATA MAESTRA QUE FALTABAN
        Route::apiResource('service-statuses', ServiceStatusesController::class);
        Route::apiResource('ticket-statuses', TicketStatusController::class);

        // Rutas para Contactos de Puntos GOB/Instituciones
        Route::apiResource('instituciones.contacts', InstitutionContactController::class)->shallow();

    }); // Fin del grupo role:superadmin

}); // Fin del grupo auth:sanctum
