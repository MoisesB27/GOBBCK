<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreferenciaRequest as StorePreferenciaRequest;
use App\Models\Preferencia;

class PreferenciaController extends Controller
{
     public function index()
    {
        $preferencias = Preferencia::with('user')->paginate(15);
        return response()->json($preferencias);
    }

    public function show($user_id)
    {
        // Dado que user_id es PK, buscamos por user_id
        $preferencia = Preferencia::with('user')->findOrFail($user_id);
        return response()->json($preferencia);
    }

    public function store(StorePreferenciaRequest  $request)
    {
        // En caso de que quieras crear o actualizar con upsert:
        $validated = $request->validated();

        $preferencia = Preferencia::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        return response()->json($preferencia, 201);
    }

    public function update(StorePreferenciaRequest  $request, $user_id)
    {
        $preferencia = Preferencia::findOrFail($user_id);
        $preferencia->update($request->validated());

        return response()->json($preferencia);
    }

    public function destroy($user_id)
    {
        $preferencia = Preferencia::findOrFail($user_id);
        $preferencia->delete();

        return response()->json(null, 204);
    }
}
