<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
// Asegúrate que el nombre del Request sea correcto
use App\Http\Requests\appointmentsRequest as StoreAppointmentRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para logging detallado
use App\Models\Actividade; // Modelo de Log Admin
use App\Models\Service;     // Modelo de Servicio
// use App\Models\Formulario; // <-- ELIMINADO: Ya no se usa
use App\Models\Historial;   // Modelo de Log Ciudadano
use App\Models\appointments;// Modelo de Cita (plural como lo usas)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    /**
     * Muestro las citas, aplicando filtros y la lógica de permisos. (CORREGIDO)
     */
    public function index(Request $request)
    {
        // 1. Cargo relaciones necesarias
        $query = appointments::with([
            'user',
            'assignedUser',
            'service.tramite', // Carga el trámite a través del servicio
            'pgob',
            'status',
            'historial' // Carga el historial (ya no 'formulario')
        ]);

        // 2. Lógica de Permisos Admin GOB
        $user = Auth::user();
        if ($user && $user->hasRole('admin-gob'))
            {
            $pgobIds = $user->adminPgobs()->pluck('id');

            $query->whereIn('pgob_id', $pgobIds);
        }

        // 3. Filtros opcionales
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        // CORREGIDO: Ordenar por 'start_time'
        $appointments = $query->latest('start_time')->paginate(15);

        return response()->json($appointments);
    }

    /**
     * Guarda una nueva cita (con datos de formulario fusionados),
     * historial y actividad. (CORREGIDO Y SIMPLIFICADO)
     */
    public function store(StoreAppointmentRequest $request)
    {
        // Log 1: Verificamos que el método store se inicia
        Log::info('AppointmentsController@store iniciado.');

        // Obtenemos todos los datos que YA FUERON VALIDADOS
        // por appointmentsRequest (el que está en el Canvas)
        $validatedData = $request->validated();
        Log::info('Datos validados:', $validatedData);

        try {
            DB::beginTransaction();
            Log::info('Transacción iniciada.');

            $user = Auth::user();
            $service = Service::with('tramite')->findOrFail($validatedData['service_id']);
            Log::info('Servicio encontrado:', ['id' => $service->id, 'name' => $service->tramite->name ?? 'N/A']);

            // 1. Preparamos los datos para la CITA (incluyendo los del formulario)
            // Usamos los datos validados y añadimos los que calcula el backend
            $appointmentData = array_merge($validatedData, [
                'user_id' => $user->id,
                'assigned_to' => null,
                'institucion_id' => $service->tramite->institucion_id,
                'pgob_id' => $service->pgob_id,
                'status_id' => 2, // 'Activo'
            ]);

            // Quitamos campos que no van en la tabla 'appointments'
            // (El 'user_id' ya lo pusimos, pero si venía del request, validatedData lo tiene)
            // Esto es solo por seguridad, el $fillable del modelo ya protege
            unset($appointmentData['datos_menor.nombre_menor']);
            unset($appointmentData['datos_menor.apellido_menor']);

            Log::info('Datos para crear cita (fusionada):', $appointmentData);

            // 2. Creamos la CITA (que ahora contiene el formulario)
            $appointment = appointments::create($appointmentData);
            Log::info('Cita (fusionada) creada con ID:', ['id' => $appointment->id, 'uuid' => $appointment->uuid]);

            // 3. Creamos el FORMULARIO (¡ELIMINADO!)
            // Ya no es necesario, los datos están en la cita.

            // 4. Guardamos el Log para el "Historial" (CIUDADANO)
            $appointment->load('status');
            if (!$appointment->status) {
                Log::error('No se pudo cargar el estado para la cita ID: ' . $appointment->id . ' con status_id: ' . $appointment->status_id);
                throw new \Exception("Estado de cita no encontrado para status_id: " . $appointment->status_id);
            }
            $historialData = [
                'appointment_id' => $appointment->id,
                'tipo_servicio_id' => $service->id,
                'entidad_id' => $service->tramite->institucion_id,
                'fecha' => $appointment->start_time->format('Y-m-d'),
                'hora' => $appointment->start_time->format('H:i:s'),
                'estado' => $appointment->status->name,
                'ticket' => $appointment->uuid,
                'detalles_ticket' => "Cita para {$service->tramite->name}",
                'user_id' => $user->id,
            ];
            Log::info('Datos para crear historial:', $historialData);
            $historial = Historial::create($historialData);
            Log::info('Historial creado con ID:', ['id' => $historial->id]);

            // 5. Guardamos el Log de Auditoría (ADMIN)
            $actividadData = [
                'user_id' => $user->id,
                'activity_type' => 'CREAR_CITA',
                'description' => "Usuario {$user->name} confirmó la cita #{$appointment->id} para {$service->tramite->name}.",
                'ip_address' => $request->ip(),
                'device_info' => $request->userAgent(),
            ];
            Log::info('Datos para crear actividad:', $actividadData);
            $actividad = Actividade::create($actividadData);
            Log::info('Actividad creada con ID:', ['id' => $actividad->id]);

            // 6. Generamos el QR "al vuelo"
            Log::info('Generación de QR omitida. Se enviará solo el UUID.');

            // 7. Confirmamos la transacción
            Log::info('Intentando hacer commit de la transacción...');
            DB::commit();
            Log::info('Transacción confirmada (commit exitoso).');

            // 8. Devolvemos la cita (El frontend usará $appointment->uuid)
            return response()->json([
                'message' => 'Cita creada exitosamente',
                'appointment' => $appointment->load(['historial', 'service.tramite', 'status']), // Ya no 'formulario'
            ], 201); // 201 Created es más correcto que 200 OK

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log 2: Capturamos errores de validación (aunque el FormRequest debería atraparlos)
                Log::error('Error de Validación en AppointmentsController@store:', [
                    'errors' => $e->errors(),
                ]);
                DB::rollBack();
                Log::info('Rollback por error de validación.');
                return response()->json(['message' => 'Datos inválidos.', 'errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            // Log 3: Capturamos cualquier otro error
            Log::error('Error general en AppointmentsController@store:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // 'trace' => $e->getTraceAsString() // Descomentar para debug extremo
            ]);

            Log::info('Intentando hacer rollback...');
            DB::rollBack();
            Log::info('Rollback completado.');

            // Devolvemos un error 500
            return response()->json([
                'message' => 'Error al crear la cita.',
                'error' => config('app.debug') ? $e->getMessage() : 'Ocurrió un error interno.'
            ], 500);
        }
    }

    /**
     * Muestro una cita específica (CORREGIDO).
     */
    public function show($id)
    {
        // CORREGIDO: Carga las relaciones correctas, quita 'formulario' y 'QRCode'
        $appointment = appointments::with([
            'user', 'assignedUser', 'service.tramite', 'pgob', 'status', 'historial'
        ])->findOrFail($id);

        $uuid = $appointment->uuid;

        // Generamos el QR "al vuelo"
        $qrCodeImage = QrCode::format('png')->size(250)->generate($uuid);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage);

        return response()->json([
            'appointment' => $appointment,
            'qr_code_base64' => $qrBase64
        ]);
    }

    /**
     * Actualizo una cita. (CORREGIDO)
     */
    public function update(StoreAppointmentRequest $request, $id) // Reutiliza el request
    {
        $appointment = appointments::findOrFail($id);

        // Obtenemos solo los datos validados que pertenecen a esta tabla
        $validatedData = $request->validated();

        // (Opcional) Debemos recalcular las FKs si el service_id cambia
        if(isset($validatedData['service_id'])) {
                $service = Service::with('tramite')->findOrFail($validatedData['service_id']);
                $validatedData['institucion_id'] = $service->tramite->institucion_id;
                $validatedData['pgob_id'] = $service->pgob_id;
            }

        $appointment->update($validatedData);

        // CONSIDERAR: Añadir un log en 'Actividade' o 'Historial' para la actualización

        return response()->json($appointment->load(['historial', 'service.tramite', 'status']));
    }

    /**
     * Elimino una cita. (Añadir lógica de log)
     */
    public function destroy($id)
    {
        $appointment = appointments::findOrFail($id);
        $user = Auth::user(); // Para el log

        // CONSIDERAR: Añadir un log en 'Actividade' o 'Historial' antes de borrar
        Log::info("Cita #{$id} será eliminada por Usuario #{$user->id}");
        // Actividade::create([... 'action' => 'CANCELAR_CITA' ...]);

        $appointment->delete();

        return response()->json(null, 204);
    }
}

