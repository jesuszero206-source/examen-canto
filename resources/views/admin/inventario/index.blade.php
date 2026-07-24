@extends('layouts.admin')

@section('title', 'Control de Inventario')

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Niveles de Stock</h5>
        
        <form action="{{ route('admin.inventario.index') }}" method="GET" class="d-flex">
            <input type="text" name="buscar" class="form-control form-control-sm me-2" placeholder="Buscar producto..." value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Producto</th>
                        <th class="text-center">Stock Actual</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Ajustar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $producto->codigo }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset($producto->imagen ?? 'images/no-image.png') }}" class="rounded" width="40" height="40" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ $producto->nombre }}</div>
                                        <div class="small text-muted">{{ $producto->categoria->nombre }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fs-5 fw-bold">{{ $producto->existencia }}</td>
                            <td class="text-center">
                                @if($producto->existencia <= 0)
                                    <span class="badge bg-danger rounded-pill">Agotado</span>
                                @elseif($producto->existencia <= 5)
                                    <span class="badge bg-warning text-dark rounded-pill">Bajo</span>
                                @else
                                    <span class="badge bg-success rounded-pill">Suficiente</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-cafe border shadow-sm rounded-3" 
                                    data-bs-toggle="modal" data-bs-target="#ajustarStockModal{{ $producto->id }}">
                                    <i class="bi bi-sliders"></i> Ajustar
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Modal Ajustar -->
                        <div class="modal fade" id="ajustarStockModal{{ $producto->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.inventario.ajustar', $producto->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                                            <h5 class="modal-title fw-bold">Ajustar Stock: {{ $producto->nombre }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="d-flex justify-content-center mb-4">
                                                <div class="text-center">
                                                    <p class="text-muted text-uppercase small fw-bold mb-1">Stock Actual</p>
                                                    <div class="display-4 fw-bold text-cafe">{{ $producto->existencia }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Tipo de Movimiento</label>
                                                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                                        <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada (+)</option>
                                                        <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>Salida (-)</option>
                                                        <option value="ajuste" {{ old('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste Directo (=)</option>
                                                    </select>
                                                    @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Cantidad</label>
                                                    <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad') }}" required min="0">
                                                    @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Motivo / Notas</label>
                                                    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror" value="{{ old('motivo') }}" placeholder="Ej: Mercancía recibida, Merma, Error de conteo" required>
                                                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    
                                                    @if(session('error'))
                                                        <div class="text-danger small mt-2 fw-bold">{{ session('error') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-cafe">Aplicar Movimiento</button>
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
    @if(method_exists($productos, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            {{ $productos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<h5 class="fw-bold mb-3 mt-5">Últimos Movimientos</h5>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Fecha</th>
                        <th>Producto</th>
                        <th>Movimiento</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos ?? [] as $mov)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold">{{ $mov->producto->nombre }}</td>
                            <td>
                                <span class="badge bg-{{ $mov->tipo_badge }} rounded-pill text-uppercase">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td>
                                @if($mov->tipo == 'entrada') <span class="text-success fw-bold">+{{ $mov->cantidad }}</span>
                                @elseif($mov->tipo == 'salida') <span class="text-danger fw-bold">-{{ $mov->cantidad }}</span>
                                @else <span class="text-warning fw-bold">{{ $mov->cantidad }}</span> @endif
                            </td>
                            <td class="small">{{ $mov->motivo }}</td>
                            <td class="small">{{ $mov->usuario->nombre ?? 'Sistema' }}</td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" data-bs-toggle="modal" data-bs-target="#editMovModal{{ $mov->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.inventario.movimiento.destroy', $mov->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 btn-delete-confirm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        
                        <!-- Modal Edit Movimiento -->
                        <div class="modal fade" id="editMovModal{{ $mov->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.inventario.movimiento.update', $mov->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                                            <h5 class="modal-title fw-bold">Editar Motivo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Motivo / Notas</label>
                                                <input type="text" name="motivo" class="form-control" value="{{ $mov->motivo }}" required>
                                            </div>
                                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Por seguridad, solo se permite editar el motivo. Para modificar cantidades, elimine el movimiento (revertirá el stock) y registre uno nuevo.</p>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-cafe">Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay movimientos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Encontrar si hay algún campo inválido dentro de un modal
        let invalidField = document.querySelector('.modal .is-invalid');
        if (invalidField) {
            let modalElement = invalidField.closest('.modal');
            if (modalElement) {
                let modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
        
        // Si hay error general y no hay campos específicos, podríamos abrir el último intentado si tuviéramos tracking,
        // pero con la validación de FormRequest, los errores caen en is-invalid.
    });
</script>
@endpush
