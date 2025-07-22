<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Appointment;

class appointment_access_log extends Model
{

    use HasFactory;

    /**
     * Indica que la clave primaria es autoincremental (id).
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'appointment_id',
        'accessed_at',
        'ip_address',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    /**
     * Relación: este log pertenece a una cita (appointment).
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(appointments::class, 'appointment_id', 'id');
    }
}
