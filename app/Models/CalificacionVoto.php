<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalificacionVoto extends Model
{
    protected $table = 'calificacion_votos';

    protected $fillable = [
        'calificacion_id',
        'user_id',
        'voto',
    ];

    public function calificacion(): BelongsTo
    {
        return $this->belongsTo(Calificacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
