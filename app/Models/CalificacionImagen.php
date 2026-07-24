<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalificacionImagen extends Model
{
    protected $table = 'calificacion_imagenes';

    protected $fillable = [
        'calificacion_id',
        'imagen',
        'nombre_original',
        'mime',
        'tamano',
    ];

    public function calificacion(): BelongsTo
    {
        return $this->belongsTo(Calificacion::class);
    }
}
