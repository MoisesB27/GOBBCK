<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentoRequest as StoreDocumentoRequest;
use Illuminate\Http\Request;
use App\Models\Documento;

class DocumentoController extends Controller
{
    // Listar documentos (opcional: filtrar por usuario)
    public function index(Request $request)
    {
        $query = Documento::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $documentos = $query->paginate(15);

        return response()->json($documentos);
    }

    // Mostrar un documento específico
    public function show($id)
    {
        $documento = Documento::with('user')->findOrFail($id);

        return response()->json($documento);
    }

    // Crear un nuevo documento
    public function store(StoreDocumentoRequest $request)
    {
        $documento = Documento::create($request->validated());

        return response()->json($documento, 201);
    }

    // Actualizar un documento existente
    public function update(StoreDocumentoRequest $request, $id)
    {
        $documento = Documento::findOrFail($id);
        $documento->update($request->validated());

        return response()->json($documento);
    }

    // Eliminar un documento
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->delete();

        return response()->json(null, 204);
    }
}
