<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Categoria::query();
        
        if ($request->has('buscar') && !empty($request->buscar)) {
            $query->where('nombre', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'LIKE', '%' . $request->buscar . '%');
        }
        
        $categorias = $query->paginate(15)->appends($request->all());
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('admin.categorias.create');
    }

    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(int $id): View
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $categoria = Categoria::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:50',
            'activa' => 'boolean',
            'orden' => 'integer|min:0'
        ]);
        
        $categoria->update($validated);
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $categoria = Categoria::findOrFail($id);
        
        if ($categoria->productos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asignados.');
        }

        $categoria->delete();
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
