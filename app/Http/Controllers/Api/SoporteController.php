<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SoporteRequest as StoreSoporteRequest;
use App\Models\Soporte;

class SoporteController extends Controller
{
    public function index()
    {
        $soportes = Soporte::with('user')->paginate(15);
        return response()->json($soportes);
    }

    public function show($id)
    {
        $soporte = Soporte::with('user')->findOrFail($id);
        return response()->json($soporte);
    }

    public function store(StoreSoporteRequest $request)
    {
        $soporte = Soporte::create($request->validated());
        return response()->json($soporte, 201);
    }

    public function update(StoreSoporteRequest $request, $id)
    {
        $soporte = Soporte::findOrFail($id);
        $soporte->update($request->validated());
        return response()->json($soporte);
    }

    public function destroy($id)
    {
        $soporte = Soporte::findOrFail($id);
        $soporte->delete();
        return response()->json(null, 204);
    }
}
