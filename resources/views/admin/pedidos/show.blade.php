@extends('layouts.admin')

@section('title', 'Detalle de Pedido #' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT))

@section('actions')
<a href="{{ route('admin.pedidos.index') }}" class="btn btn-light shadow-sm">
    <i class="bi bi-arrow-left me-1"></i> Volver a Pedidos
</a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0">Artículos del Pedido</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->detalles as $detalle)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($detalle->producto->imagen ?? 'images/no-image.png') }}" class="rounded shadow-sm" width="40" height="40" style="object-fit: cover;">
                                        <div class="fw-bold">{{ $detalle->producto->nombre }}</div>
                                    </div>
                                </td>
                                <td class="text-center">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-end pe-4 fw-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <div class="d-flex justify-content-end">
                    <div class="text-end" style="min-width: 200px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>${{ number_format($pedido->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Impuestos:</span>
                            <span>${{ number_format($pedido->impuesto, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <strong class="fs-5">Total:</strong>
                            <strong class="fs-5">{{ $pedido->total_formateado }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Información del Cliente -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0">Información del Cliente</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: bold; font-size: 1.2rem;">
                        {{ substr($pedido->user->nombre, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $pedido->user->nombre_completo }}</div>
                        <div class="text-muted">{{ $pedido->user->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado del Pedido -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0">Estado del Pedido</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold">Estado Actual</label>
                        <div>
                            <span class="badge bg-{{ $pedido->estado_badge }} rounded-pill fs-6 text-uppercase px-3 py-2">
                                {{ str_replace('_', ' ', $pedido->estado) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold">Actualizar Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ $pedido->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completado" {{ $pedido->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ $pedido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-cafe w-100">Guardar Cambios</button>
                </form>
            </div>
        </div>
        
        <!-- Notas del Pedido -->
        @if($pedido->notas)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0">Notas del Cliente</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0 text-muted">{{ $pedido->notas }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
