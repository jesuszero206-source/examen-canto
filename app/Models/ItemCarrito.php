<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCarrito extends Model
{
    protected $table = 'item_carritos';

    protected $fillable = ['carrito_id', 'producto_id', 'cantidad'];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
        ];
    }

    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->producto->precio * $this->cantidad;
    }
}
