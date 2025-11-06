<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- CONTROLADORES ---
use App\Http\Controllers\Api\PgobController;
use App\Http\Controllers\Api\InstitucionesController;
use App\Http\Controllers\Api\TramiteController;
use App\Http\Controllers\Api\ServiceController;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\TestimonioController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\SoporteController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\PreferenciaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GobPointAdminController;
use App\Http\Controllers\Api\TicketStatusController;
use App\Http\Controllers\Api\TicketPriorityController;
use App\Http\Controllers\Api\ServiceStatusesController;
use App\Http\Controllers\Api\AppointmentAccessLogController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\ActividadeController;
use App\Http\Controllers\Api\InstitutionContactController;


// Autenticación
Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

// GESTIÓN DE PUNTOS GOB
Route::controller(PgobController::class)->prefix('pgobs')->group(function () {

// Ruta para la función de geolocalización (Búsqueda de Cercanía)
    Route::get('nearby', [PgobController::class, 'findNearby'])->name('pgobs.nearby');

});

// RUTAS DE CONSULTA DE DATA MAESTRA (AHORA PÚBLICAS)
Route::apiResource('ubicaciones', UbicacionController::class)->only(['index', 'show']);
Route::apiResource('testimonios', TestimonioController::class)->only(['index', 'show']);
Route::apiResource('instituciones', InstitucionesController::class)->only(['index', 'show']);
Route::apiResource('tramites', TramiteController::class)->only(['index', 'show']);
Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
Route::apiResource('pgobs', PgobController::class)->only(['index','show']); // Incluye la lista de PGOBS



// ====================================================================
// 2. RUTAS AUTENTICADAS (CLIENTE Y ADMIN) - SOLO TRANSACCIONES
// ====================================================================
Route::middleware('auth:sanctum')->group(function () {

    // A. PERFIL Y AUTENTICACIÓN
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('change-password', [LoginController::class, 'changePassword']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);

    // B. MÓDULO DE CITAS Y SOPORTE (Cliente)
    Route::apiResource('appointments', AppointmentsController::class)->only(['index', 'show', 'store', 'update']); // Index, show son privados. Store es transaccional.
    Route::apiResource('historiales', HistorialController::class)->only(['index', 'show', 'store']);
    Route::apiResource('soportes', SoporteController::class)->only(['index', 'show', 'store']);


    // C. UTILIDADES Y NOTIFICACIONES
    Route::apiResource('notificaciones', NotificacionController::class)->only(['index', 'show', 'store']);
    Route::apiResource('preferencias', PreferenciaController::class)->only(['index', 'show', 'store', 'update', 'destroy']);


    // ====================================================================
    // 3. RUTAS DE ADMINISTRACIÓN OPERACIONAL (ROL: ADMIN)
    // ====================================================================

    $adminMiddleware = RoleMiddleware::class . ':admin';
    Route::middleware($adminMiddleware)->group(function () {
        // GESTIÓN DE CITAS Y SOPORTE (El delegado solo actualiza los tickets de su Pgob)
        Route::apiResource('admin/soportes', SoporteController::class)->only(['index', 'show', 'update']);
        Route::apiResource('admin/appointments', AppointmentsController::class)->only(['index', 'show', 'update']);
    }); // Fin del grupo role:admin


    // ====================================================================
    // 4. RUTAS DE ADMINISTRACIÓN GLOBAL (ROL: SUPERADMIN)
    // ====================================================================

    $superAdminMiddleware = RoleMiddleware::class . ':superadmin';

    Route::middleware($superAdminMiddleware)->group(function () {

        // GESTIÓN DE USUARIOS (CRUD completo y asegurado)
        Route::apiResource('users', UserController::class);

        // GESTIÓN DE PUNTOS GOB
        Route::apiResource('pgobs', PgobController::class)->except(['index', 'show']);

        // GESTIÓN DE ADMINS GOB (Asignación)
        Route::post('pgobs/{pgob}/admins', [GobPointAdminController::class, 'store']);
        Route::get('pgobs/{pgob}/admins', [GobPointAdminController::class, 'index']);
        Route::delete('pgobs/{pgob}/admins/{user}', [GobPointAdminController::class, 'destroy']);


        // GESTIÓN DE DATA MAESTRA DE SOPORTE (CRUD)
        Route::apiResource('ticket-statuses', TicketStatusController::class);
        Route::apiResource('ticket-priorities', TicketPriorityController::class);

        // GESTIÓN DE ESTRUCTURA Y DATA MAESTRA (CRUD completo)
        Route::apiResource('service-statuses', ServiceStatusesController::class);
        Route::apiResource('instituciones', InstitucionesController::class)->except(['index', 'show']);
        Route::apiResource('tramites', TramiteController::class)->except(['index', 'show']);
        Route::apiResource('services', ServiceController::class)->except(['index', 'show']);

        // GESTIÓN DE OTROS MÓDULOS DE ADMINISTRACIÓN
        Route::apiResource('actividades', ActividadeController::class);
        Route::apiResource('documentos', DocumentoController::class);
        Route::apiResource('ubicaciones', UbicacionController::class)->except(['index', 'show']);
        Route::apiResource('appointment-access-logs', AppointmentAccessLogController::class);
        Route::apiResource('instituciones.contacts', InstitutionContactController::class)->shallow();

    }); // Fin del grupo role:superadmin

}); // Fin del grupo auth:sanctum
