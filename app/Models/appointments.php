<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // Importamos HasOne
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Service;
use App\Models\Pgob;
use App\Models\Instituciones;      // Modelo de Instituciones (plural)
use App\Models\AppointmentStatus; // Modelo de Estados de Cita
use App\Models\Historial;        // Modelo de Historial


class appointments extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden llenar masivamente. (CORREGIDO Y FUSIONADO)
     * Estos deben coincidir 100% con la migración y el controlador.
     */
    protected $fillable = [
        // Campos de la Cita
        'user_id',
        'service_id',
        'institucion_id', // Para backoffice
        'pgob_id',        // Para backoffice
        'status_id',      // Estado de la cita
        'start_time',
        'end_time',
        'assigned_to',

        // Campos del Formulario (Fusionados)
        'nombre',
        'apellido',
        'cedula',
        'email',
        'telefono',
        'direccion',
        'tiene_discapacidad',
        'tipo_beneficiario',
        'datos_menor',
        // 'uuid' se genera automáticamente, no necesita estar en fillable
    ];

    /**
     * Genera el UUID automáticamente al crear.
     */
    protected static function booted(): void
    {
        static::creating(function ($appointment) {
            // Asegura que siempre se genere un UUID al crear
            if (empty($appointment->uuid)) {
                $appointment->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Casts para atributos especiales. (CORREGIDO Y FUSIONADO)
     * Convierte las columnas a tipos de datos específicos.
     */
    protected $casts = [
        'start_time' => 'datetime', // Castear a objeto Carbon/DateTime
        'end_time' => 'datetime',   // Castear a objeto Carbon/DateTime
        'tiene_discapacidad' => 'boolean', // Nuevo
        'datos_menor' => 'json',     // Nuevo
    ];

    // --- RELACIONES ---

    /**
     * Relación: Cita pertenece a un usuario que la reservó.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: La cita está asignada a un usuario (atendedor).
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relación: Cita pertenece a un servicio.
     */
    public function service(): BelongsTo
    {
        // Asegúrate de que tu modelo se llame Service.php (singular)
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Relación: Cita pertenece a un Punto Gob (para backoffice).
     */
    public function pgob(): BelongsTo
    {
        // Asegúrate de que tu modelo se llame Pgob.php (singular)
        return $this->belongsTo(Pgob::class, 'pgob_id');
    }

    /**
     * Relación: Cita pertenece a una Institución (para backoffice).
     */
    public function institucion(): BelongsTo
    {
        // Usamos el nombre plural confirmado por ti
        return $this->belongsTo(Instituciones::class, 'institucion_id');
    }

    /**
     * Relación: Cita pertenece a un Estado.
     */
    public function status(): BelongsTo
    {
        // Asegúrate de que tu modelo se llame AppointmentStatus.php (singular)
        return $this->belongsTo(AppointmentStatus::class, 'status_id');
    }

    /**
     * Relación: Cita tiene un registro de Historial asociado. (Uno a Uno)
     */
    public function historial(): HasOne
    {
        // Asumiendo que 'historials' tiene 'appointment_id' como clave foránea
        return $this->hasOne(Historial::class, 'appointment_id');
    }

}

