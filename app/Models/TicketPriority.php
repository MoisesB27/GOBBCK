<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketPriority extends Model
{
        use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'ticket_priorities';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color_code',
        'is_active',
        'description',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Una prioridad puede tener muchos tickets (Soportes).
     */
    public function soportes(): HasMany
    {
        // Asume que la tabla 'soportes' tiene la llave foránea 'priority_id'
        return $this->hasMany(Soporte::class, 'priority_id');
    }
}
