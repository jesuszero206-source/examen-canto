@extends('layouts.admin')

@section('title', 'Productos')

@section('actions')
<a href="{{ route('admin.productos.store') }}" class="btn btn-cafe shadow-sm" onclick="event.preventDefault(); document.getElementById('create-form-container').classList.toggle('d-none');">
    <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
</a>
@endsection

@section('content')
<!-- Formulario Inline Oculto -->
<div id="create-form-container" class="card border-0 shadow-sm rounded-4 mb-4 {{ $errors->any() ? '' : 'd-none' }}">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Agregar Nuevo Producto</h5>
        <button type="button" class="btn-close" onclick="document.getElementById('create-form-container').classList.add('d-none');"></button>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.productos._form', ['producto' => new \App\Models\Producto()])
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light me-2" onclick="document.getElementById('create-form-container').classList.add('d-none');">Cancelar</button>
                <button type="submit" class="btn btn-cafe">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
        <form action="{{ route('admin.productos.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o código..." value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-dark px-4"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset($producto->imagen ?? 'images/no-image.png') }}" class="rounded shadow-sm" width="50" height="50" style="object-fit: cover;">
                                    <div class="fw-bold">{{ $producto->nombre }}</div>
                                </div>
                            </td>
                            <td class="text-muted fw-semibold">{{ $producto->codigo }}</td>
                            <td><span class="badge bg-secondary rounded-pill">{{ $producto->categoria->nombre }}</span></td>
                            <td class="text-center fw-bold">${{ number_format($producto->precio, 2) }}</td>
                            <td class="text-center">
                                @if($producto->existencia <= 0)
                                    <span class="badge bg-danger rounded-pill">0</span>
                                @elseif($producto->existencia <= 5)
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $producto->existencia }}</span>
                                @else
                                    <span class="badge bg-success rounded-pill">{{ $producto->existencia }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($producto->disponible)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Disponible</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Oculto</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-3 me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <form action="{{ route('admin.productos.destroy', $producto->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3" 
                                        onclick="return confirm('¿Eliminar producto de forma lógica?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($productos->hasPages())
    <div class="card-footer bg-white border-0 pt-4">
        {{ $productos->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
