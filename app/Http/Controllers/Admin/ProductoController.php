<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->query('buscar');
        $query = Producto::with('categoria');
        
        if ($buscar) {
            $query->buscar($buscar);
        }
        
        $productos = $query->paginate(15);
        return view('admin.productos.index', compact('productos', 'buscar'));
    }

    public function create(): View
    {
        $categorias = Categoria::all();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('images/productos', 'public');
        }

        $producto = Producto::create($data);
        
        AuditLog::registrar('create', 'productos', $producto->id, null, $producto->toArray());

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $anteriores = $producto->toArray();
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('images/productos', 'public');
        }

        $producto->update($data);
        
        AuditLog::registrar('update', 'productos', $producto->id, $anteriores, $producto->toArray());

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $anteriores = $producto->toArray();
        
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        
        $producto->delete();
        
        AuditLog::registrar('delete', 'productos', $producto->id, $anteriores, null);

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
