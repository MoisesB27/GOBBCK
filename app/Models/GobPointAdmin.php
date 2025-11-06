<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Pgob;
class GobPointAdmin extends Pivot
{
    use HasFactory;
    protected $table = 'gob_point_admins';
    protected $fillable = [

        'user_id',
        'pgob_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pgob()
    {
        return $this->belongsTo(Pgob::class, 'pgob_id');
    }
}
