<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Pgob;
use App\Models\Service;
use App\Models\Appointments;



class Formulario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // usuario que envía el formulario (puede ser nullable si anónimo)
        'pgob_id',        // Punto Gob relacionado
        'service_id',     // Servicio vinculado al formulario
        'appointment_id', // Cita vinculada (opcional)
        'data',           // Datos JSON del formulario flexible
        'status',         // Estado del formulario (ej: pendiete, aprobado, rechazado)
        'submitted_at',   // Fecha y hora de envío
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

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
        return $this->belongsTo(appointments::class, 'appointment_id', 'id');
    }
}
