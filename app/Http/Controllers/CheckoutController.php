<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetallePedido;
use App\Models\InventarioMovimiento;
use App\Models\Pedido;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $carrito = Carrito::with('items.producto')->where('user_id', Auth::id())->first();
        
        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        return view('checkout.index', compact('carrito'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'notas' => 'nullable|string|max:500'
        ]);

        $carrito = Carrito::with('items.producto')->where('user_id', Auth::id())->first();

        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($carrito->items as $item) {
                if ($item->producto->existencia < $item->cantidad) {
                    throw new \Exception("No hay suficiente stock para {$item->producto->nombre}");
                }
                $subtotal += $item->producto->precio * $item->cantidad;
            }

            $impuesto = $subtotal * 0.16;
            $total = $subtotal + $impuesto;

            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'estado' => 'pendiente',
                'metodo_pago' => $request->metodo_pago,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'notas' => $request->notas
            ]);

            foreach ($carrito->items as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->producto->precio,
                    'subtotal' => $item->producto->precio * $item->cantidad
                ]);

                $producto = $item->producto;
                $stockAnterior = $producto->existencia;
                $producto->existencia -= $item->cantidad;
                $producto->save();

                InventarioMovimiento::create([
                    'producto_id' => $producto->id,
                    'user_id' => Auth::id(),
                    'tipo' => 'salida',
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->existencia,
                    'motivo' => "Venta pedido #{$pedido->id}"
                ]);
            }

            $carrito->items()->delete();

            AuditLog::registrar('create', 'pedidos', $pedido->id, null, $pedido->toArray());

            DB::commit();

            return redirect()->route('checkout.confirmacion', $pedido->id)->with('success', 'Pedido realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    public function confirmacion(int $id): View|RedirectResponse
    {
        $pedido = Pedido::with('detalles.producto')->where('user_id', Auth::id())->findOrFail($id);
        return view('checkout.confirmacion', compact('pedido'));
    }
}
