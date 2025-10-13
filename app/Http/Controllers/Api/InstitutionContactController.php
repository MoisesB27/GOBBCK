<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instituciones; // Necesito la institución padre
use App\Models\InstitutionContact; // Mi modelo de contacto
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstitutionContactController extends Controller
{
    /**
     * Muestro todos los contactos que tengo para una Institución específica.
     * (Asumo que esta ruta está anidada: /instituciones/{institucion}/contacts)
     */
    public function index(Instituciones $institucion)
    {
        // Cargo los contactos usando la relación que definí en mi modelo Instituciones.
        $contacts = $institucion->contacts;
        return response()->json($contacts);
    }

    /**
     * Guardo un nuevo contacto para esta Institución.
     */
    public function store(Request $request, Instituciones $institucion)
    {
        // 1. Valido los datos: tipo, valor, y si es principal.
        $validated = $request->validate([
            'tipo' => ['required', Rule::in(['correo', 'telefono', 'whatsapp', 'otro'])],
            'valor' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'principal' => 'sometimes|boolean',
        ]);

        // 2. Asocio el contacto a mi institución y lo creo.
        $contact = $institucion->contacts()->create($validated);

        return response()->json($contact, 201);
    }

    /**
     * Muestro un contacto individual.
     */
    public function show(InstitutionContact $contact)
    {
        return response()->json($contact);
    }

    /**
     * Actualizo un contacto existente.
     */
    public function update(Request $request, InstitutionContact $contact)
    {
        // Valido solo los campos que pueden cambiar.
        $validated = $request->validate([
            'tipo' => ['sometimes', Rule::in(['correo', 'telefono', 'whatsapp', 'otro'])],
            'valor' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'principal' => 'sometimes|boolean',
        ]);

        $contact->update($validated);

        return response()->json($contact);
    }

    /**
     * Elimino un contacto.
     */
    public function destroy(InstitutionContact $contact)
    {
        $contact->delete();
        return response()->json(null, 204);
    }
}
