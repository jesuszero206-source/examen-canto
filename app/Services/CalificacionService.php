<?php

namespace App\Services;

use App\Models\Calificacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\CalificacionImagen;
use Illuminate\Support\Facades\Storage;
use Exception;

class CalificacionService
{
    /**
     * Verifica si el usuario ha comprado el producto y el pedido está completado.
     */
    public function puedeCalificar(int $userId, int $productoId): bool
    {
        return Pedido::where('user_id', $userId)
            ->where('estado', 'completado')
            ->whereHas('detalles', function ($query) use ($productoId) {
                $query->where('producto_id', $productoId);
            })
            ->exists();
    }

    /**
     * Obtiene el ID del pedido validado para la reseña.
     */
    public function getPedidoValidado(int $userId, int $productoId): ?int
    {
        $pedido = Pedido::where('user_id', $userId)
            ->where('estado', 'completado')
            ->whereHas('detalles', function ($query) use ($productoId) {
                $query->where('producto_id', $productoId);
            })
            ->latest()
            ->first();

        return $pedido ? $pedido->id : null;
    }

    /**
     * Crea o actualiza una calificación.
     */
    public function guardarCalificacion(int $userId, int $productoId, array $data, $imagenes = null): Calificacion
    {
        if (!$this->puedeCalificar($userId, $productoId)) {
            throw new Exception("Debes haber comprado este producto para poder calificarlo y el pedido debe estar completado.");
        }

        $pedidoId = $this->getPedidoValidado($userId, $productoId);

        $calificacion = Calificacion::updateOrCreate(
            ['user_id' => $userId, 'producto_id' => $productoId],
            [
                'pedido_id' => $pedidoId,
                'calificacion' => $data['calificacion'],
                'comentario' => $data['comentario'] ?? null,
                'etiquetas' => $data['etiquetas'] ?? null,
                // si se crea nuevo por defecto es visible
            ]
        );

        // Subir nuevas imagenes si existen
        if ($imagenes && is_array($imagenes)) {
            // Contar cuantas imagenes tiene actualmente
            $currentImagesCount = $calificacion->imagenes()->count();
            
            foreach ($imagenes as $img) {
                if ($currentImagesCount >= 3) break;
                
                $path = $img->store('calificaciones', 'public');
                
                $calificacion->imagenes()->create([
                    'imagen' => $path,
                    'nombre_original' => $img->getClientOriginalName(),
                    'mime' => $img->getMimeType(),
                    'tamano' => $img->getSize(),
                ]);
                
                $currentImagesCount++;
            }
        }

        return $calificacion;
    }

    /**
     * Recalcula y actualiza el promedio de un producto de forma eficiente.
     */
    public function recalcularPromedioProducto(int $productoId): void
    {
        $producto = Producto::find($productoId);
        if (!$producto) return;

        // Solo cuenta reseñas visibles
        $stats = Calificacion::where('producto_id', $productoId)
            ->where('estado', 'visible')
            ->selectRaw('COUNT(*) as total, AVG(calificacion) as promedio')
            ->first();

        $producto->update([
            'total_calificaciones' => $stats->total ?? 0,
            'promedio_calificacion' => round($stats->promedio ?? 0, 2),
        ]);
    }
}