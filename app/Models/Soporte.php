<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Pgob;
use App\Models\TicketStatus;

class Soporte extends Model
{

    use HasFactory;

    protected $table = 'soportes';

    /**
     * Los campos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pgob_id',
        'status_id',
        'nombre_completo',
        'correo_electronico',
        'asunto',
        'descripcion',
    ];

    /**
     * Relación: Un soporte puede pertenecer a un usuario (o ser nulo).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un ticket pertenece a un Punto GOB.
     *
     * @return BelongsTo
     */
    public function pgob(): BelongsTo
    {
        return $this->belongsTo(Pgob::class, 'pgob_id');
    }

    /**
     * Relación: Un ticket tiene un estado.
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        // Se relaciona con la tabla de referencia ticket_statuses
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }
}
