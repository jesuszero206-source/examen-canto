<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles');
        
        if ($request->has('buscar') && !empty($request->buscar)) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('apellido', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('username', 'LIKE', '%' . $request->buscar . '%');
            });
        }
        
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }
        
        $usuarios = $query->paginate(15)->appends($request->all());
        $roles = Role::all();
        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['activo'] = $request->has('activo');
        
        $user = User::create($data);
        
        if ($request->has('role_id')) {
            $user->roles()->attach($request->role_id);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(int $id): View
    {
        $usuario = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $usuario = User::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'username' => 'required|string|max:50|unique:users,username,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'activo' => 'boolean',
            'role_id' => 'required|exists:roles,id'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $validated['activo'] = $request->has('activo');

        $usuario->update($validated);
        
        $usuario->roles()->sync([$request->role_id]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (Auth::id() == $id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario = User::findOrFail($id);
        $usuario->delete();
        
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
