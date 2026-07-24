<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MesaController extends Controller
{
    public function index(): View
    {
        $mesas = Mesa::all();
        return view('admin.mesas.index', compact('mesas'));
    }

    public function create(): View
    {
        return view('admin.mesas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'numero' => 'required|string|max:50|unique:mesas,numero',
            'nombre' => 'nullable|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'required|in:interior,terraza,balcon,jardin,ventana',
            'estado' => 'required|in:disponible,reservada,ocupada,limpieza,fuera_de_servicio',
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            $mesa = Mesa::create($request->all());
            AuditLog::registrar('create', 'mesas', $mesa->id, null, $mesa->toArray());
            DB::commit();

            return redirect()->route('admin.mesas.index')->with('success', 'Mesa creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la mesa: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Mesa $mesa): View
    {
        return view('admin.mesas.edit', compact('mesa'));
    }

    public function update(Request $request, Mesa $mesa): RedirectResponse
    {
        $request->validate([
            'numero' => 'required|string|max:50|unique:mesas,numero,' . $mesa->id,
            'nombre' => 'nullable|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'required|in:interior,terraza,balcon,jardin,ventana',
            'estado' => 'required|in:disponible,reservada,ocupada,limpieza,fuera_de_servicio',
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            $anteriores = $mesa->toArray();
            $mesa->update($request->all());
            AuditLog::registrar('update', 'mesas', $mesa->id, $anteriores, $mesa->toArray());
            DB::commit();

            return redirect()->route('admin.mesas.index')->with('success', 'Mesa actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la mesa: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Mesa $mesa): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($mesa->reservas()->whereIn('estado', ['pendiente', 'confirmada'])->exists()) {
                throw new \Exception('No se puede eliminar la mesa porque tiene reservas activas.');
            }
            $anteriores = $mesa->toArray();
            $mesa->delete();
            AuditLog::registrar('delete', 'mesas', $mesa->id, $anteriores, null);
            DB::commit();

            return redirect()->route('admin.mesas.index')->with('success', 'Mesa eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
