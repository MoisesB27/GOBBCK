<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePgobRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     * Solo un superadmin puede crear o modificar Puntos GOB.
     */
    public function authorize(): bool
    {
        // Asume que solo el superadmin puede gestionar Puntos GOB.
        return Auth::check() && Auth::user()->hasRole('superadmin');
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        // Array base con los días de la semana
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $pgobId = $this->route('pgob'); // Para ignorar el ID actual en las validaciones de unicidad si es UPDATE

        $rules = [
            // 1. CAMPOS DE LA TABLA 'PGOBS'
            'name'                      => ['required', 'string', 'max:255', Rule::unique('pgobs', 'name')->ignore($pgobId)],
            'descripcion'               => 'nullable|string',
            'appointment_limit'         => 'required|integer|min:0',
            'appointment_limit_per_user'=> 'nullable|integer|min:0',
            'is_active'                 => 'required|boolean',

            // 2. CAMPOS DE HORARIOS (fusionados en la tabla pgobs)
            'business_hours'            => 'required|array',

            // 3. CAMPOS DE LA UBICACIÓN (anidados dentro del request)
            'ubicacion'                 => 'required|array',
            'ubicacion.latitude'        => 'required|numeric|between:-90,90',
            'ubicacion.longitude'       => 'required|numeric|between:-180,180',
            'ubicacion.address'         => 'required|string|max:500', // Dirección legible
            'ubicacion.city'            => 'required|string|max:100',
            'ubicacion.state'           => 'required|string|max:100',
            'ubicacion.tipo'            => 'nullable|string|max:50', // Tipo de sucursal

            // 4. CAMPOS OPCIONALES DE CONTACTO DE UBICACION (No esenciales)
            'ubicacion.zip_code'        => 'nullable|string|max:10',
            'ubicacion.contacto'        => 'nullable|string|max:255',
            'ubicacion.radio_cobertura' => 'nullable|integer|min:0',
            'ubicacion.extras'          => 'nullable|array',
        ];

        // Reglas para los Horarios (business_hours)
        foreach ($days as $day) {
            $rules["business_hours.{$day}"] = 'nullable|array';
            $rules["business_hours.{$day}.open"] = 'required_with:business_hours.' . $day . '|date_format:H:i';
            $rules["business_hours.{$day}.close"] = 'required_with:business_hours.' . $day . '|date_format:H:i';
            $rules["business_hours.{$day}.appointments"] = 'required_with:business_hours.' . $day . '|boolean';
        }

        return $rules;
    }
}
