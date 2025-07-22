<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Instituciones;
use App\Http\Requests\InstitucionesRequest as StoreInstitucionesRequest;

class InstitucionesController extends Controller
{
    public function index()
    {
        $instituciones = Instituciones::with(['tramites', 'services'])->paginate(15);
        return response()->json($instituciones);
    }

    public function show($id)
    {
        $institucion = Instituciones::with(['tramites', 'services'])->findOrFail($id);
        return response()->json($institucion);
    }

    public function store(StoreInstitucionesRequest $request)
    {
        $institucion = Instituciones::create($request->validated());
        return response()->json($institucion, 201);
    }

    public function update(StoreInstitucionesRequest $request, $id)
    {
        $institucion = Instituciones::findOrFail($id);
        $institucion->update($request->validated());
        return response()->json($institucion);
    }

    public function destroy($id)
    {
        $institucion = Instituciones::findOrFail($id);
        $institucion->delete();
        return response()->json(null, 204);
    }
}
