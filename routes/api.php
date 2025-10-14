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
use App\Http\Controllers\Api\InstitutionContactController; // Controlador de Contactos
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
use App\Http\Controllers\Api\ServiceStatusesController; // Data Maestra
use App\Http\Controllers\Api\TicketStatusController; // Data Maestra
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
//    Estas rutas aplican a todos los usuarios logueados (incluso clientes)
// ====================================================================
Route::middleware('auth:sanctum')->group(function () {

    // Autenticación y Perfil
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('change-password', [LoginController::class, 'changePassword']);

    // Perfil del Usuario Logueado (GET /api/profile)
    // El método 'show' aquí cargará el perfil del usuario autenticado, no por ID
    Route::get('profile', [ProfileController::class, 'show']);

    // Módulos transaccionales y de usuario (la seguridad se gestiona DENTRO del controlador)
    Route::apiResource('historiales', HistorialController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::apiResource('soportes', SoporteController::class)->only(['index', 'show', 'store']);
    Route::apiResource('appointments', AppointmentsController::class)->only(['index', 'show', 'store', 'update']);
    Route::apiResource('notificaciones', NotificacionController::class)->only(['index', 'show', 'store']);
    Route::apiResource('preferencias', PreferenciaController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Rutas de consulta pública que se pueden mover fuera del grupo auth:sanctum
    Route::apiResource('pgobs', PgobController::class)->only(['index', 'show']);
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
    Route::apiResource('tramites', TramiteController::class)->only(['index', 'show']);
    Route::apiResource('instituciones', InstitucionesController::class)->only(['index', 'show']);

    // ====================================================================
    // 3. RUTAS DE ADMINISTRACIÓN (CRUD COMPLETO ASEGURADO)
    //    Requieren el rol 'super-admin' para todas las operaciones (store, update, destroy)
    // ====================================================================

    // Definimos el middleware de roles con la sintaxis robusta de FQCN
    $roleMiddleware = RoleMiddleware::class . ':super-admin';

    Route::middleware($roleMiddleware)->group(function () {

        // GESTIÓN DE USUARIOS (CRUD completo y asegurado)
        Route::apiResource('users', UserController::class)->except(['index', 'show']);

        // GESTIÓN DE CONTENIDO (CRUD completo y asegurado)
        Route::apiResource('actividades', ActividadeController::class)->except(['index', 'show']);
        Route::apiResource('documentos', DocumentoController::class)->except(['index', 'show']);
        Route::apiResource('formularios', FormularioController::class)->except(['index', 'show']);
        Route::apiResource('testimonios', TestimonioController::class)->except(['index', 'show']);

        // GESTIÓN DE ESTRUCTURA Y DATA MAESTRA (CRUD completo y asegurado)
        Route::apiResource('pgobs', PgobController::class)->except(['index', 'show']);
        Route::apiResource('services', ServiceController::class)->except(['index', 'show']);
        Route::apiResource('tramites', TramiteController::class)->except(['index', 'show']);
        Route::apiResource('instituciones', InstitucionesController::class)->except(['index', 'show']);

        // Rutas Anidadas y Tablas de PIVOTE/LOGS
        Route::apiResource('appointment-services', AppointmentServiceController::class);
        Route::apiResource('appointment-access-logs', AppointmentAccessLogController::class);

        // CONTROLADORES DE DATA MAESTRA QUE FALTABAN
        Route::apiResource('service-statuses', ServiceStatusesController::class);
        Route::apiResource('ticket-statuses', TicketStatusController::class);

        // Rutas para Contactos de Puntos GOB/Instituciones
        Route::apiResource('instituciones.contacts', InstitutionContactController::class)->shallow();
        // Ejemplo: GET /api/pgobs/1/contacts
        // Route::apiResource('pgobs.contacts', PuntoGobContactController::class)->shallow();

    }); // Fin del grupo role:super-admin

}); // Fin del grupo auth:sanctum
