<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointments;
use App\Models\Appointment_Access_Log;

class Historial extends Model
{
    protected $fillable = [
        'appointment_id',
        'tipo_servicio_id',
        'entidad_id',
        'fecha',
        'hora',
        'estado',
        'ticket',
        'detalles_ticket',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'time',
    ];

    // Usuario asociado
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Tipo de servicio asociado (Service)
    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'tipo_servicio_id');
    }

    // Entidad (Institucion)
    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Instituciones::class, 'entidad_id');
    }

    // Cita asociada
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(appointments::class);
    }

    // Access Logs asociados a la cita
    public function appointmentAccessLogs(): HasMany
    {
        return $this->hasMany(Appointment_Access_Log::class, 'appointment_id', 'appointment_id');
    }
}
