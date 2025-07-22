<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest as StoreServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['tramite', 'institucion', 'pgob'])->paginate(15);
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::with(['tramite', 'institucion', 'pgob'])->findOrFail($id);
        return response()->json($service);
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());
        return response()->json($service, 201);
    }

    public function update(StoreServiceRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());
        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(null, 204);
    }
}
