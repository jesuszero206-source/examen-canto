<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use App\Events\ResenaActualizada;
use App\Events\ResenaEliminada;

class OpinionController extends Controller
{
    public function index(Request $request)
    {
        $query = Calificacion::with(['producto', 'user']);

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('producto_id') && $request->producto_id != '') {
            $query->where('producto_id', $request->producto_id);
        }

        $opiniones = $query->latest()->paginate(15);
        $productos = \App\Models\Producto::orderBy('nombre')->get();

        return view('admin.opiniones.index', compact('opiniones', 'productos'));
    }

    public function cambiarEstado(Request $request, int $id)
    {
        $request->validate(['estado' => 'required|in:visible,oculto,reportado']);
        
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->update(['estado' => $request->estado]);

        event(new ResenaActualizada($calificacion));

        return back()->with('success', 'Estado de la opinión actualizado.');
    }

    public function responder(Request $request, int $id)
    {
        $request->validate(['respuesta_admin' => 'required|string|max:1000']);
        
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->update(['respuesta_admin' => $request->respuesta_admin]);

        return back()->with('success', 'Respuesta enviada correctamente.');
    }

    public function destroy(int $id)
    {
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->delete();

        event(new ResenaEliminada($calificacion));

        return back()->with('success', 'Opinión eliminada.');
    }
}
