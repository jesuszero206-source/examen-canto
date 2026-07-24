<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function show(int $id): View
    {
        $producto = Producto::where('id', $id)->disponibles()->firstOrFail();

        $relacionados = Producto::disponibles()
            ->porCategoria($producto->categoria_id)
            ->where('id', '!=', $producto->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Cargar reseñas con paginación (visibles)
        $calificaciones = $producto->calificaciones()
            ->with(['user', 'imagenes', 'votos'])
            ->where('estado', 'visible')
            ->latest()
            ->paginate(5);

        // Calcular distribución de estrellas
        $distribucion = [];
        $total = $producto->total_calificaciones > 0 ? $producto->total_calificaciones : 1; // evitar division por cero
        
        for ($i = 5; $i >= 1; $i--) {
            $count = $producto->calificaciones()->where('estado', 'visible')->where('calificacion', $i)->count();
            $distribucion[$i] = [
                'count' => $count,
                'porcentaje' => round(($count / $total) * 100)
            ];
        }

        // Verificar si el usuario ya calificó
        $miCalificacion = null;
        $puedeCalificar = false;
        
        if (\Illuminate\Support\Facades\Auth::check()) {
            $miCalificacion = $producto->calificaciones()
                ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->first();
                
            if (!$miCalificacion) {
                // Verificar si puede calificar
                $calificacionService = app(\App\Services\CalificacionService::class);
                $puedeCalificar = $calificacionService->puedeCalificar(\Illuminate\Support\Facades\Auth::id(), $producto->id);
            }
        }

        return view('producto.detalle', compact('producto', 'relacionados', 'calificaciones', 'distribucion', 'miCalificacion', 'puedeCalificar'));
    }
}
