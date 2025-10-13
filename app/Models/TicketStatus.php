<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $table = 'ticket_statuses';

    protected $fillable = ['name', 'priority_level', 'color_code', 'is_active'];

    // Relación One-to-Many: Un estado puede tener muchos tickets (Soportes).
    public function soportes()
    {
        return $this->hasMany(Soporte::class, 'status_id');
    }
}
