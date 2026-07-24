<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'categoria_id', 'codigo', 'nombre', 'descripcion',
        'precio', 'existencia', 'disponible', 'imagen',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'existencia' => 'integer',
            'disponible' => 'boolean',
        ];
    }

    // Relationships
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function itemsCarrito(): HasMany
    {
        return $this->hasMany(ItemCarrito::class);
    }

    public function detallesPedido(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)->where('existencia', '>', 0);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%");
    }

    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Helpers
    public function getStockStatusAttribute(): string
    {
        if ($this->existencia <= 0) return 'agotado';
        if ($this->existencia <= 5) return 'bajo';
        if ($this->existencia <= 15) return 'medio';
        return 'alto';
    }

    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio, 2);
    }
}
