<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\TramiteRequest; // Assuming you have a request class for validation
use App\Http\Controllers\Controller;
use App\Models\Tramite;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = Tramite::all();
        return response()->json($tramites);
    }

    public function store(TramiteRequest $request)
    {
        $tramite = Tramite::create($request->validated());
        return response()->json($tramite, 201);
    }

    public function show(Tramite $tramite)
    {
        return response()->json($tramite);
    }

    public function update(TramiteRequest $request, Tramite $tramite)
    {
        $tramite->update($request->validated());
        return response()->json($tramite);
    }

    public function destroy(Tramite $tramite)
    {
        $tramite->delete();
        return response()->json(null, 204);
    }
}
