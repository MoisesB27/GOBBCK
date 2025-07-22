<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentServiceRequest as StoreAppointmentServiceRequest;
use App\Models\AppointmentService;

class AppointmentServiceController extends Controller
{
    public function index()
    {
        $services = AppointmentService::with(['tramite', 'institucion', 'pgob'])->paginate(15);

        return response()->json($services);
    }

    public function show($id)
    {
        $service = AppointmentService::with(['tramite', 'institucion', 'pgob', 'appointments', 'formularios'])->findOrFail($id);

        return response()->json($service);
    }

    public function store( $request)
    {
        $service = AppointmentService::create($request->validated());

        return response()->json($service, 201);
    }

    public function update(StoreAppointmentServiceRequest $request, $id)
    {
        $service = AppointmentService::findOrFail($id);
        $service->update($request->validated());

        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = AppointmentService::findOrFail($id);
        $service->delete();

        return response()->json(null, 204);
    }
}
