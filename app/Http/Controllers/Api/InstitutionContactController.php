<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instituciones;
use App\Models\InstitutionContact;
use App\Http\Requests\InstitutionContactRequest;


class InstitutionContactController extends Controller
{
    /**
     * Muestro todos los contactos que tengo para una Institución específica.
     */
    public function index(Instituciones $institucion)
    {
        $contacts = $institucion->contacts()->orderByDesc('principal')->get();
        return response()->json($contacts);
    }

    /**
     * Guardo un nuevo contacto para esta Institución, usando el Form Request para la validación.
     */
    public function store(InstitutionContactRequest $request, Instituciones $institucion)
    {
        $validated = $request->validated();

        // 1. **SOLUCIÓN DEL ERROR 1048**: Añadimos explícitamente el ID de la institución
        // al array validado, asegurando que esté presente cuando se llame a `create()`.
        $data = array_merge($validated, [
            'institucion_id' => $institucion->id,
        ]);

        // 2. Si el nuevo contacto se marca como principal (true), desmarcamos a los demás.
        if (isset($data['principal']) && $data['principal']) {
            $institucion->contacts()->update(['principal' => false]);
        }

        // 3. Creamos el nuevo contacto usando el array $data completo.
        // Nota: En este caso, ya que estamos pasando el ID explícitamente, podríamos
        // usar InstitutionContact::create($data); en lugar de la relación,
        // pero usar la relación también funciona si el ID está en el array.
        $contact = $institucion->contacts()->create($data);

        return response()->json($contact, 201);
    }

    /**
     * Muestro un contacto individual.
     */
    public function show(Instituciones $institucion, InstitutionContact $contact)
    {
        if ($contact->institucion_id !== $institucion->id) {
            // Usar abort(404) es más idiomático para not found
            abort(404, 'Contacto no encontrado para esta institución.');
        }

        return response()->json($contact);
    }

    /**
     * Actualizo un contacto existente, usando el Form Request para la validación.
     */
    public function update(InstitutionContactRequest $request, Instituciones $institucion, InstitutionContact $contact)
    {
        $validated = $request->validated();

        // 1. Si se intenta marcar este contacto como principal (principal = true):
        if (isset($validated['principal']) && $validated['principal']) {
            // Desmarcamos a los demás contactos de esta institución.
            $institucion->contacts()
                        ->where('id', '!=', $contact->id)
                        ->update(['principal' => false]);
        }

        // 2. Actualizamos el contacto.
        $contact->update($validated);

        return response()->json($contact);
    }

    /**
     * Elimino un contacto.
     */
    public function destroy(Instituciones $institucion, InstitutionContact $contact)
    {
        if ($contact->institucion_id !== $institucion->id) {
            abort(403, 'No autorizado para eliminar este contacto.');
        }

        $contact->delete();
        return response()->json(null, 204);
    }
}
