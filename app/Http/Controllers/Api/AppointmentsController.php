<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\appointmentsRequest as StoreAppointmentRequest;
use App\Models\appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importo esto para obtener el usuario actual

class AppointmentsController extends Controller
{
    /**
     * Muestro las citas, aplicando filtros y la lógica de permisos.
     */
    public function index(Request $request)
    {
        // 1. Cargo mis relaciones necesarias.
        $query = appointments::with([
            'user',
            'assignedUser',
            'service',
            'pgob',
            'status'
        ]);

        // 2. Lógica de Permisos (¡CRÍTICO!): Restrinjo el acceso si soy Admin GOB.
        $user = Auth::user();
        if ($user && $user->hasRole('admin-gob')) {
            // Obtengo los IDs de los Puntos GOB que yo administro.
            $pgobIds = $user->adminPgobs()->pluck('id');
            // ¡Solo muestro las citas asignadas a esos Puntos GOB!
            $query->whereIn('pgob_id', $pgobIds);
        }

        // 3. Filtros opcionales (aplicables a todos los usuarios, incluyendo Super Admins).
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Uso 'status_id' para filtrar (la llave foránea normalizada).
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        // Finalizo la consulta y pagino.
        $appointments = $query->latest('date')->latest('time')->paginate(15);

        return response()->json($appointments);
    }

    /**
     * Muestro una cita específica.
     */
    public function show($id)
    {
        // Cargo la cita y todas sus relaciones.
        $appointment = appointments::with([
            'user',
            'assignedUser',
            'service',
            'pgob',
            'status'
        ])->findOrFail($id);

        return response()->json($appointment);
    }

    /**
     * Guardo una nueva cita.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = appointments::create($request->validated());
        return response()->json($appointment, 201);
    }

    /**
     * Actualizo una cita.
     */
    public function update(StoreAppointmentRequest $request, $id)
    {
        $appointment = appointments::findOrFail($id);
        $appointment->update($request->validated());
        return response()->json($appointment);
    }

    /**
     * Elimino una cita.
     */
    public function destroy($id)
    {
        $appointment = appointments::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }
}
