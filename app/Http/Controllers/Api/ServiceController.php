<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest as StoreServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Aplico la capa de seguridad para que solo los super-admins puedan gestionar servicios.
     */
    public function __construct()
    {
        $this->middleware(['role:super-admin']);
    }

    /**
     * Muestro todos mis servicios, incluyendo sus relaciones clave y el estado.
     */
    public function index()
    {
        //  Cargo las relaciones: trámite, institución, Punto GOB y la nueva relación 'status'.
        $services = Service::with(['tramite', 'institucion', 'pgob', 'status'])->paginate(15);
        return response()->json($services);
    }

    /**
     * Muestro un servicio específico.
     */
    public function show($id)
    {
        //  Cargo el servicio y todas sus relaciones asociadas, incluyendo 'status'.
        $service = Service::with(['tramite', 'institucion', 'pgob', 'status'])->findOrFail($id);
        return response()->json($service);
    }

    /**
     * Guardo un nuevo servicio.
     */
    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());
        return response()->json($service, 201);
    }

    /**
     * Actualizo un servicio existente.
     */
    public function update(StoreServiceRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());
        return response()->json($service);
    }

    /**
     * Elimino un servicio.
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(null, 204);
    }
}
