<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tramite;
use App\Models\Instituciones;
use App\Models\Pgob;
use App\Models\appointments;
use App\Models\Formulario;
class AppointmentService extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'service_id',
        'quantity',
        'special_requests',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];


    /**
     * Relación: un servicio pertenece a un trámite.
     */
    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'tramite_id', 'id');
    }

    /**
     * Relación: un servicio pertenece a una institución.
     */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Instituciones::class, 'institucion_id', 'id');

    }

    /**
     * Relación: un servicio puede pertenecer a un PGOB.
     */
    public function pgob(): BelongsTo
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }

    /**
     * Relación: un servicio puede tener muchas citas (appointments).
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(appointments::class, 'service_id', 'id');
    }

    /**
     * Relación: un servicio puede tener muchos formularios asociados.
     */
    public function formularios(): HasMany
    {
        return $this->hasMany(Formulario::class, 'service_id', 'id');
    }
}
