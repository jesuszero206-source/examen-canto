<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\CalificacionVoto;
use App\Services\CalificacionService;
use App\Events\ResenaCreada;
use App\Events\ResenaEliminada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    protected CalificacionService $calificacionService;

    public function __construct(CalificacionService $calificacionService)
    {
        $this->calificacionService = $calificacionService;
    }

    public function store(Request $request, int $productoId)
    {
        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'string|max:50',
            'imagenes' => 'nullable|array|max:3',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        try {
            $calificacion = $this->calificacionService->guardarCalificacion(
                Auth::id(),
                $productoId,
                $request->only(['calificacion', 'comentario', 'etiquetas']),
                $request->file('imagenes')
            );

            event(new ResenaCreada($calificacion));

            return response()->json([
                'success' => true,
                'message' => 'Gracias por compartir tu opinión.',
                'calificacion' => $calificacion->load('imagenes')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id)
    {
        $calificacion = Calificacion::where('user_id', Auth::id())->findOrFail($id);
        $productoId = $calificacion->producto_id;
        $calificacion->delete();

        event(new ResenaEliminada($calificacion));

        return response()->json(['success' => true, 'message' => 'Reseña eliminada correctamente.']);
    }

    public function votar(Request $request, int $id)
    {
        $request->validate(['voto' => 'required|in:1,-1']);
        
        $calificacion = Calificacion::findOrFail($id);

        CalificacionVoto::updateOrCreate(
            ['calificacion_id' => $id, 'user_id' => Auth::id()],
            ['voto' => $request->voto]
        );

        return response()->json(['success' => true, 'message' => 'Voto registrado.']);
    }
}
