<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
class Actividade extends Model

{
    use HasFactory;

    /**
     * Campos para asignación masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'device_info',
    ];

    /**
     * Relación: una actividad pertenece a un usuario.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
