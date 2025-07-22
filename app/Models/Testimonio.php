<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Testimonio extends Model
{

    use HasFactory;
    /**
     * Los campos que pueden asignarse masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'message',
        'rating',
        'photo_url',
    ];

    /**
     * Casting opcional si quieres tratar rating como entero.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relación: Un testimonio pertenece a un usuario.
     */ 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');  
    }
}
