<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Service;
use App\Models\Pgob;

class Notificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'fecha',
        'publico',
        'tipo',
        'is_read',
        'metadata',
        'service_id',
        'pgob_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'publico' => 'boolean',
        'is_read' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * La notificación pertenece a un usuario (puede ser null si es pública)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación opcional con servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación opcional con punto gob
     */
    public function pgob(): BelongsTo
    {
        return $this->belongsTo(Pgob::class);
    }
}
