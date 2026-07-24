<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\ItemCarrito;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index(): View
    {
        $carrito = Carrito::with('items.producto')->firstOrCreate([
            'user_id' => Auth::id()
        ]);

        return view('carrito.index', compact('carrito'));
    }

    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = Carrito::firstOrCreate(['user_id' => Auth::id()]);
        
        $producto = Producto::findOrFail($request->producto_id);
        
        if ($producto->existencia < $request->cantidad) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No hay suficiente stock para este producto.'], 400);
            }
            return back()->with('error', 'No hay suficiente stock para este producto.');
        }

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($item) {
            if ($producto->existencia < ($item->cantidad + $request->cantidad)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'No puedes agregar más, no hay suficiente stock.'], 400);
                }
                return back()->with('error', 'No puedes agregar más, no hay suficiente stock.');
            }
            $item->cantidad += $request->cantidad;
            $item->save();
        } else {
            ItemCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $producto->precio
            ]);
        }

        if ($request->wantsJson()) {
            $totalItems = $carrito->items()->sum('cantidad');
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito.',
                'carritoItemCount' => $totalItems
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function aumentar(int $id): RedirectResponse
    {
        $item = ItemCarrito::whereHas('carrito', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $producto = $item->producto;

        if ($producto->existencia <= $item->cantidad) {
            return back()->with('error', 'No hay más stock disponible.');
        }

        $item->increment('cantidad');

        return back()->with('success', 'Cantidad aumentada.');
    }

    public function disminuir(int $id): RedirectResponse
    {
        $item = ItemCarrito::whereHas('carrito', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        if ($item->cantidad > 1) {
            $item->decrement('cantidad');
            return back()->with('success', 'Cantidad disminuida.');
        } else {
            $item->delete();
            return back()->with('success', 'Producto eliminado del carrito.');
        }
    }

    public function eliminar(int $id): RedirectResponse
    {
        $item = ItemCarrito::whereHas('carrito', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $item->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar(): RedirectResponse
    {
        $carrito = Carrito::where('user_id', Auth::id())->first();
        if ($carrito) {
            $carrito->items()->delete();
        }

        return back()->with('success', 'Carrito vaciado exitosamente.');
    }
}
