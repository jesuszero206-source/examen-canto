<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pedido::with('user')->latest();
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('id', 'like', "%{$buscar}%")
                  ->orWhereHas('user', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellido', 'like', "%{$buscar}%");
                  });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->paginate(15)->withQueryString();
        
        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido): View
    {
        $pedido->load(['user', 'detalles.producto']);
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,completado,cancelado'
        ]);

        $pedido->update([
            'estado' => $request->estado
        ]);

        if (in_array($request->estado, ['completado', 'cancelado'])) {
            if ($pedido->mesa_id) {
                $mesa = $pedido->mesa;
                if ($mesa) {
                    $mesa->update(['estado' => 'disponible']);
                }
            }
        }

        return redirect()->route('admin.pedidos.show', $pedido->id)
                         ->with('success', 'El estado del pedido ha sido actualizado.');
    }
}
