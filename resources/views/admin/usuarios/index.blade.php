@extends('layouts.admin')

@section('title', 'Usuarios')

@section('actions')
<button type="button" class="btn btn-cafe shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
    <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
</button>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Listado de Usuarios</h5>
        
        <form action="{{ route('admin.usuarios.index') }}" method="GET" class="d-flex gap-2">
            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach($roles ?? [] as $r)
                    <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <div class="input-group input-group-sm">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar usuario..." value="{{ request('buscar') }}">
                <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Rol</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ substr($usuario->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $usuario->username }}</div>
                                        <div class="text-muted small">{{ $usuario->email ?? 'Sin email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->nombre_completo }}</td>
                            <td>
                                @foreach($usuario->roles as $rol)
                                    <span class="badge bg-dark rounded-pill">{{ $rol->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if($usuario->activo)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Activo</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-3 me-1" 
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $usuario->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                @if($usuario->id !== Auth::id())
                                <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3" 
                                        onclick="return confirm('¿Deshabilitar este usuario?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Modal Edit -->
                        <div class="modal fade" id="editUserModal{{ $usuario->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold">Editar Usuario</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nombre *</label>
                                                    <input type="text" name="nombre" class="form-control" value="{{ $usuario->nombre }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Apellido</label>
                                                    <input type="text" name="apellido" class="form-control" value="{{ $usuario->apellido }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Rol *</label>
                                                    <select name="role_id" class="form-select" required>
                                                        @foreach($roles ?? [] as $r)
                                                            <option value="{{ $r->id }}" {{ $usuario->roles->contains($r->id) ? 'selected' : '' }}>{{ $r->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Estado</label>
                                                    <select name="activo" class="form-select" {{ $usuario->id === Auth::id() ? 'disabled' : '' }}>
                                                        <option value="1" {{ $usuario->activo ? 'selected' : '' }}>Activo</option>
                                                        <option value="0" {{ !$usuario->activo ? 'selected' : '' }}>Inactivo</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="alert alert-info py-2 small mb-0">
                                                        Nota: Para cambiar contraseña, el usuario debe hacerlo desde su perfil.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-cafe">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($usuarios, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            {{ $usuarios->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Create -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario *</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña *</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="4">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido</label>
                            <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}">
                            @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol *</label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="" disabled {{ !old('role_id') ? 'selected' : '' }}>Selecciona un rol</option>
                                @foreach($roles ?? [] as $r)
                                    <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="activo" class="form-select @error('activo') is-invalid @enderror">
                                <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('activo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-cafe">Crear Usuario</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            var createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
            createUserModal.show();
        @endif
    });
</script>
@endpush
@endsection
