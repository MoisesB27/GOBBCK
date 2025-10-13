<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Pgob;
use App\Models\Service;
use App\Models\Appointments; // <-- CORRECCIÓN: Usar singular y PascalCase


class Formulario extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'pgob_id',
        'tipo_de_tramite',
        'tipo_de_beneficiario',
        'service_id',
        'appointment_id',
        'status_id',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    // --- RELACIONES MANY-TO-ONE (BELONGS TO) ---

    /**
     * Relación: formulario pertenece a usuario (opcional).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relación: formulario pertenece a Punto Gob.
     */
    public function pgob()
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }

    /**
     * Relación: formulario pertenece a un servicio.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Relación: formulario pertenece a una cita (opcional).
     */
    public function appointment()
    {
        // CORRECCIÓN: Usamos el modelo Appointment (singular)
        return $this->belongsTo(appointments::class, 'appointment_id', 'id');
    }

    /**
     * Relación: formulario tiene un estado.
     * CRÍTICO para el backoffice.
     */
    public function status()
    {
        // Asume que existe un modelo FormularioStatus
        return $this->belongsTo(FormularioStatus::class, 'status_id');
    }
}
