<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'file_url',
        'status',
        'verified_at',
    ];

    /**
     * Casts para atributos especiales.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Relación: Documento pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
