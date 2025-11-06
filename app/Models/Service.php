<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany
use App\Models\Tramite;
use App\Models\Pgob;
use App\Models\service_statuses; // Usar nombre singular CamelCase por convención
use App\Models\appointments; // Usar el nombre plural que tienes

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'description',
        'duration',
        'logo',
        'tramite_id',
        'ubicacion',
        'status_id',
        'pgob_id',
    ];

    /**
     * Casts para atributos con tipos específicos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
    ];

    /**
     * Relación con tramite.
     */
    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    /**
     * Relación con punto gob.
     */
    public function pgob(): BelongsTo
    {
        return $this->belongsTo(Pgob::class, 'pgob_id');
    }

    /**
     * Relación con el estado del servicio.
     */
    public function status(): BelongsTo
    {
        // Asegúrate que tu modelo se llame ServiceStatus.php
        return $this->belongsTo(service_statuses::class, 'status_id');
    }

    /**
     * Relación: un Servicio puede tener muchas Citas. (CORREGIDO)
     */
    public function appointments(): HasMany // <-- Cambiado a HasMany
    {
        // La tabla 'appointments' tiene la llave foránea 'service_id'
        return $this->hasMany(appointments::class, 'service_id');
    }
}
