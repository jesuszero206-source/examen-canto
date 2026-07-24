<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Producto::with('categoria');
        
        if ($request->has('buscar') && !empty($request->buscar)) {
            $query->where('nombre', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'LIKE', '%' . $request->buscar . '%');
        }
        
        $productos = $query->paginate(20);
        $movimientos = InventarioMovimiento::with(['producto', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('admin.inventario.index', compact('productos', 'movimientos'));
    }

    public function edit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        $movimientos = InventarioMovimiento::where('producto_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
            
        return view('admin.inventario.edit', compact('producto', 'movimientos'));
    }

    public function ajustar(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($id);
            $anteriores = $producto->toArray();
            
            $stockAnterior = $producto->existencia;
            $cantidad = $request->cantidad;
            $tipo = $request->tipo;
            
            if ($tipo === 'ajuste') {
                if ($cantidad > $stockAnterior) {
                    $diferencia = $cantidad - $stockAnterior;
                    $producto->existencia = $cantidad;
                    $cantidad = $diferencia;
                    $tipo = 'entrada';
                } elseif ($cantidad < $stockAnterior) {
                    $diferencia = $stockAnterior - $cantidad;
                    $producto->existencia = $cantidad;
                    $cantidad = $diferencia;
                    $tipo = 'salida';
                } else {
                    throw new \Exception('La cantidad de ajuste es igual al stock actual.');
                }
            } elseif ($tipo === 'salida') {
                if ($stockAnterior < $cantidad) {
                    throw new \Exception('No hay suficiente stock para realizar esta operación.');
                }
                $producto->existencia -= $cantidad;
            } else {
                $producto->existencia += $cantidad;
            }
            
            $stockNuevo = $producto->existencia;
            $producto->save();

            $movimiento = InventarioMovimiento::create([
                'producto_id' => $producto->id,
                'user_id' => Auth::id(),
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $request->motivo
            ]);

            AuditLog::registrar('ajuste_inventario', 'productos', $producto->id, $anteriores, $producto->toArray());

            DB::commit();
            return back()->with('success', 'Inventario ajustado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
    public function updateMovimiento(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $movimiento = InventarioMovimiento::findOrFail($id);
            $movimiento->motivo = $request->motivo;
            $movimiento->save();

            return back()->with('success', 'Movimiento actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyMovimiento(int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $movimiento = InventarioMovimiento::findOrFail($id);
            $producto = $movimiento->producto;

            if ($movimiento->tipo === 'entrada') {
                if ($producto->existencia < $movimiento->cantidad) {
                    throw new \Exception('No se puede eliminar porque resultaría en un stock negativo.');
                }
                $producto->existencia -= $movimiento->cantidad;
            } else {
                $producto->existencia += $movimiento->cantidad;
            }
            $producto->save();
            
            $movimiento->delete();
            
            AuditLog::registrar('eliminar_movimiento', 'inventario_movimientos', $id, $movimiento->toArray(), []);
            
            DB::commit();
            return back()->with('success', 'Movimiento eliminado y stock revertido.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
