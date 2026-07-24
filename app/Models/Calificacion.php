<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calificacion extends Model
{
    use SoftDeletes;

    protected $table = 'calificaciones';

    protected $fillable = [
        'producto_id',
        'user_id',
        'pedido_id',
        'calificacion',
        'comentario',
        'etiquetas',
        'estado',
        'respuesta_admin',
    ];

    protected function casts(): array
    {
        return [
            'calificacion' => 'integer',
            'etiquetas' => 'array',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(CalificacionImagen::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(CalificacionVoto::class);
    }

    // Scopes
    public function scopeVisibles($query)
    {
        return $query->where('estado', 'visible');
    }
}
