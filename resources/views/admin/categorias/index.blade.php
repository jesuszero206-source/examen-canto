@extends('layouts.admin')

@section('title', 'Categorías')

@section('actions')
<button type="button" class="btn btn-cafe shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoriaModal">
    <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
</button>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Listado de Categorías</h5>
        
        <form action="{{ route('admin.categorias.index') }}" method="GET" class="d-flex">
            <input type="text" name="buscar" class="form-control form-control-sm me-2" placeholder="Buscar categoría..." value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Orden</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-cafe-light text-cafe rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi {{ $categoria->icono ?? 'bi-tag' }} fs-5"></i>
                                    </div>
                                    <span class="fw-bold">{{ $categoria->nombre }}</span>
                                </div>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($categoria->descripcion, 50) ?? '--' }}</td>
                            <td class="text-center">
                                @if($categoria->activa)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Activa</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $categoria->orden }}</td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-3 me-1" 
                                    data-bs-toggle="modal" data-bs-target="#editCategoriaModal{{ $categoria->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3" 
                                        onclick="return confirm('¿Eliminar esta categoría?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        
                        <!-- Modal Edit -->
                        <div class="modal fade" id="editCategoriaModal{{ $categoria->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold">Editar Categoría</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nombre *</label>
                                                <input type="text" name="nombre" class="form-control" value="{{ $categoria->nombre }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Icono (Clase Bootstrap)</label>
                                                <input type="text" name="icono" class="form-control" value="{{ $categoria->icono }}" placeholder="Ej: bi-cup-hot">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Descripción</label>
                                                <textarea name="descripcion" class="form-control" rows="2">{{ $categoria->descripcion }}</textarea>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Orden</label>
                                                    <input type="number" name="orden" class="form-control" value="{{ $categoria->orden }}" min="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Estado</label>
                                                    <select name="activa" class="form-select">
                                                        <option value="1" {{ $categoria->activa ? 'selected' : '' }}>Activa</option>
                                                        <option value="0" {{ !$categoria->activa ? 'selected' : '' }}>Inactiva</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-cafe">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($categorias, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            {{ $categorias->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Create -->
<div class="modal fade" id="createCategoriaModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.categorias.store') }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icono (Clase Bootstrap)</label>
                        <input type="text" name="icono" class="form-control @error('icono') is-invalid @enderror" value="{{ old('icono') }}" placeholder="Ej: bi-cup-hot">
                        @error('icono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" name="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                            @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="activa" class="form-select @error('activa') is-invalid @enderror">
                                <option value="1" {{ old('activa', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ old('activa') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                            @error('activa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-cafe">Guardar Categoría</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            var createModal = new bootstrap.Modal(document.getElementById('createCategoriaModal'));
            createModal.show();
        @endif
    });
</script>
@endpush
@endsection
