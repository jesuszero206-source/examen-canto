<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioMovimiento extends Model
{
    public $timestamps = false;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'producto_id', 'user_id', 'tipo', 'cantidad',
        'stock_anterior', 'stock_nuevo', 'motivo',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'stock_anterior' => 'integer',
            'stock_nuevo' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (InventarioMovimiento $movimiento) {
            $movimiento->created_at = now();
        });
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTipoBadgeAttribute(): string
    {
        return match ($this->tipo) {
            'entrada' => 'success',
            'salida' => 'danger',
            'ajuste' => 'warning',
            default => 'secondary',
        };
    }
}
