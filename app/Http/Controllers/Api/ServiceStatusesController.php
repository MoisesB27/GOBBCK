<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceStatusRequest; // Asumo que tengo este Request Form
use App\Models\ServiceStatus ; // Asumo que tengo este Modelo



class ServiceStatusesController extends Controller
{
    /**
     * Aplico la capa de seguridad para que solo los super-admins puedan gestionar estados.
     */
    public function __construct()
    {
        // Seguridad: Solo usuarios con rol 'super-admin' pueden acceder a esta data maestra
        $this->middleware(['role:super-admin']);
    }

    /**
     * Muestro todos los estados de servicio, ordenados y paginados.
     */
    public function index()
    {
        // Ordeno por nombre para una mejor navegación en el backoffice
        $statuses = ServiceStatus::orderBy('name')->paginate(15);
        return response()->json($statuses);
    }

    /**
     * Muestro un estado de servicio específico.
     */
    public function show($id)
    {
        $status = ServiceStatus::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Guardo un nuevo estado de servicio.
     */
    public function store(ServiceStatusRequest $request)
    {
        // Utilizo el Request Form para validar y crear
        $status = ServiceStatus::create($request->validated());
        return response()->json($status, 201);
    }

    /**
     * Actualizo un estado de servicio existente.
     */
    public function update(ServiceStatusRequest $request, $id)
    {
        $status = ServiceStatus::findOrFail($id);
        $status->update($request->validated());
        return response()->json($status);
    }

    /**
     * Elimino un estado de servicio.
     */
    public function destroy($id)
    {
        $status = ServiceStatus::findOrFail($id);

        // ⚠️ Nota: En un entorno de producción, debería verificar si hay Servicios
        // usando este estado antes de permitir la eliminación.

        $status->delete();
        return response()->json(null, 204);
    }
}
