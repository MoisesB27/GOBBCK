<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Service;
use App\Models\Pgob;

class appointments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',           // Usuario que reservó la cita
        'service_id',        // Servicio para la cita
        'pgob_id',           // Punto Gob donde se realizará la cita
        'status_id',        // Estado de la cita (relación con AppointmentStatus)
        'appointment_date',  // Fecha y hora programada
        'status',            // Estado de la cita (ej: pendiente, confirmada, cancelada)
        'assigned_to',       // Usuario asignado para atender la cita (opcional)
        'qr_code',           // QR para acceso
        'comments',          // Comentarios adicionales
    ];

    /**
     * Casts para atributos especiales.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * Relación: Cita pertenece a un usuario que la reservó.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relación: La cita está asignada a un usuario (atendedor).
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    /**
     * Relación: Cita pertenece a un servicio.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Relación: Cita pertenece a un Punto Gob.
     */
    public function pgob(): BelongsTo
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }
}
