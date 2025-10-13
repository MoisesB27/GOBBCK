<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStatusRequest; // Asumo que tengo este Request Form
use App\Models\TicketStatus; // Asumo que tengo este Modelo

class TicketStatusController extends Controller
{
    /**
     * Aplico la capa de seguridad para que solo los super-admins puedan gestionar estados.
     */
    public function __construct()
    {
        //  Seguridad: Solo usuarios con rol 'super-admin' pueden acceder a esta data maestra
        $this->middleware(['role:super-admin']);
    }

    /**
     * Muestro todos los estados de ticket, ordenados y paginados.
     */
    public function index()
    {
        // Cargo y ordeno los estados para el backoffice.
        $statuses = TicketStatus::orderBy('priority_level')->orderBy('name')->paginate(15);
        return response()->json($statuses);
    }

    /**
     * Muestro un estado de ticket específico.
     */
    public function show($id)
    {
        $status = TicketStatus::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Guardo un nuevo estado de ticket.
     */
    public function store(TicketStatusRequest $request)
    {
        // Utilizo el Request Form para validar y crear.
        $status = TicketStatus::create($request->validated());
        return response()->json($status, 201);
    }

    /**
     * Actualizo un estado de ticket existente.
     */
    public function update(TicketStatusRequest $request, $id)
    {
        $status = TicketStatus::findOrFail($id);
        $status->update($request->validated());
        return response()->json($status);
    }

    /**
     * Elimino un estado de ticket.
     */
    public function destroy($id)
    {
        $status = TicketStatus::findOrFail($id);

        $status->delete();
        return response()->json(null, 204);
    }
}
