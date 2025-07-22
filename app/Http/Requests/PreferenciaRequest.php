<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenciaRequest extends FormRequest
{
    public function authorize()
    {
        // Ajusta esta lógica en función de tus permisos y auth
        return true;
    }

    public function rules()
    {
        return [
            'user_id'                 => 'required|string|exists:users,id',
            'theme'                   => 'nullable|string|max:255',
            'notification_preferences'=> 'nullable|array',
            'privacy_settings'        => 'nullable|array',
        ];
    }
}
