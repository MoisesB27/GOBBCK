<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\FormularioRequest as StoreFormularioRequest;;
use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index(Request $request)
    {
        $query = Formulario::with(['user', 'pgob', 'service', 'appointment']);

        // Filtros opcionales
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $formularios = $query->paginate(15);

        return response()->json($formularios);
    }

    public function show($id)
    {
        $formulario = Formulario::with(['user', 'pgob', 'service', 'appointment'])->findOrFail($id);

        return response()->json($formulario);
    }

    public function store(StoreFormularioRequest $request)
    {
        $formulario = Formulario::create($request->validated());

        return response()->json($formulario, 201);
    }

    public function update(StoreFormularioRequest $request, $id)
    {
        $formulario = Formulario::findOrFail($id);
        $formulario->update($request->validated());

        return response()->json($formulario);
    }

    public function destroy($id)
    {
        $formulario = Formulario::findOrFail($id);
        $formulario->delete();

        return response()->json(null, 204);
    }
}
