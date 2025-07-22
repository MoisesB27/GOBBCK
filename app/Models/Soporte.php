<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Soporte extends Model
{

    use HasFactory;

    /**
     * Los campos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
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
}
