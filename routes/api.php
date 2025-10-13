<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActividadeController;
use App\Http\Controllers\Api\AppointmentAccessLogController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\AppointmentServiceController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\FormularioController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\InstitucionesController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\PgobController;
use App\Http\Controllers\Api\PreferenciaController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SoporteController;
use App\Http\Controllers\Api\TestimonioController;
use App\Http\Controllers\Api\TramiteController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\LoginController;


// ======================= Authentication =======================
// POST     /login           login
// POST     /logout          logout
//Route::apiResource('login', LoginController::class)
//->only(['login', 'logout', 'register', 'changePassword']);
Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);


// ======================= Authentication Middleware =======================
Route::middleware('auth:sanctum')->group(function () {
Route::post('logout', [LoginController::class, 'logout']);
Route::post('change-password', [LoginController::class, 'changePassword']);
Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'destroy', 'update']);
Route::apiResource('historiales', HistorialController::class)->only(['index', 'show', 'store', 'destroy']);
Route::apiResource('profiles', ProfileController::class)->only(['index', 'show', 'destroy','store']);
Route::apiResource('soportes', SoporteController::class) ->only(['index', 'show', 'store', 'destroy']);
});


// ======================= ACTIVIDADES =======================
// GET      /actividades           index
// GET      /actividades/{id}      show
// POST     /actividades           store
Route::apiResource('actividades', ActividadeController::class)
    ->only(['index', 'show','destroy', 'store']);

// ======================= APPOINTMENT ACCESS LOGS =======================
// GET      /appointment-access-logs           index
// GET      /appointment-access-logs/{id}      show
// POST     /appointment-access-logs           store
// DELETE   /appointment-access-logs/{id}      destroy
Route::apiResource('appointment-access-logs', AppointmentAccessLogController::class)
    ->only(['index', 'show', 'store', 'destroy']);

// ======================= APPOINTMENT SERVICES =======================
// GET      /appointment-services           index
// GET      /appointment-services/{id}      show
// POST     /appointment-services           store
// PUT      /appointment-services/{id}      update
// PATCH    /appointment-services/{id}      update
// DELETE   /appointment-services/{id}      destroy
Route::apiResource('AppointmentServices', AppointmentServiceController::class)
->only(['index', 'show', 'store', 'destroy']);

// ======================= APPOINTMENTS =======================
// GET      /appointments           index
// GET      /appointments/{id}      show
// POST     /appointments           store
// PUT      /appointments/{id}      update
// PATCH    /appointments/{id}      update
// DELETE   /appointments/{id}      destroy
Route::apiResource('appointments', AppointmentsController::class)
->only(['index', 'show', 'store', 'destroy']);

// ======================= DOCUMENTOS =======================
// GET      /documentos           index
// GET      /documentos/{id}      show
// POST     /documentos           store
// PUT      /documentos/{id}      update
// PATCH    /documentos/{id}      update
// DELETE   /documentos/{id}      destroy
Route::apiResource('documentos', DocumentoController::class)
->only(['index', 'show', 'store', 'destroy']);

// ======================= FORMULARIOS =======================
// GET      /formularios           index
// GET      /formularios/{id}      show
// POST     /formularios           store
// PUT      /formularios/{id}      update
// PATCH    /formularios/{id}      update
// DELETE   /formularios/{id}      destroy
Route::apiResource('formularios', FormularioController::class)
->only(['index', 'show', 'store', 'destroy']);


// ======================= INSTITUCIONES =======================
// GET      /instituciones           index
// GET      /instituciones/{id}      show
// POST     /instituciones           store
// PUT      /instituciones/{id}      update
// PATCH    /instituciones/{id}      update
// DELETE   /instituciones/{id}      destroy
Route::apiResource('instituciones', InstitucionesController::class)->
only(['index', 'show', 'store', 'destroy', 'update']);


// ======================= NOTIFICACIONES =======================
// GET      /notificaciones           index
// GET      /notificaciones/{id}      show
// POST     /notificaciones           store
// PUT      /notificaciones/{id}      update
// PATCH    /notificaciones/{id}      update
// DELETE   /notificaciones/{id}      destroy
Route::apiResource('notificaciones', NotificacionController::class)
    ->only(['index', 'show', 'store', 'destroy']);

// ======================= PREFERENCIAS =======================
// GET      /preferencias           index
// GET      /preferencias/{id}      show
// POST     /preferencias           store
// PUT      /preferencias/{id}      update
// PATCH    /preferencias/{id}      update
// DELETE   /preferencias/{id}      destroy
Route::apiResource('preferencias', PreferenciaController::class)
    ->only(['index', 'show', 'store', 'destroy']);

// ======================= PGOBS =======================
// GET      /pgobs           index
// GET      /pgobs/{id}      show
// POST     /pgobs           store
// PUT      /pgobs/{id}      update
// PATCH    /pgobs/{id}      update
// DELETE   /pgobs/{id}      destroy
Route::apiResource('pgobs', PgobController::class)
    ->only(['index', 'show', 'store', 'destroy']);


// ======================= SERVICES =======================
// GET      /services           index
// GET      /services/{id}      show
// POST     /services           store
// PUT      /services/{id}      update
// PATCH    /services/{id}      update
// DELETE   /services/{id}      destroy
Route::apiResource('services', ServiceController::class)->
only(['index', 'show', 'store', 'destroy']);

// ======================= UBICACIONES =======================
// GET      /ubicaciones           index
// GET      /ubicaciones/{id}      show
Route::apiResource('ubicaciones', UbicacionController::class)
    ->only(['index', 'show']);

// ======================= TRAMITES =======================
// GET      /tramites           index
// GET      /tramites/{id}      show
// POST     /tramites           store
// PUT      /tramites/{id}      update
// PATCH    /tramites/{id}      update
// DELETE   /tramites/{id}      destroy
Route::apiResource('tramites', TramiteController::class)
->only(['index', 'show', 'store', 'destroy']);



// ======================= TESTIMONIOS (pública) =======================
// GET      /testimonios           index
Route::apiResource('testimonios', TestimonioController::class)
->only(['index', 'show', 'store', 'destroy']);
