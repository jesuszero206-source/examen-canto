@extends('layouts.admin')

@section('title', 'Pedidos')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
        <form action="{{ route('admin.pedidos.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por ID o cliente..." value="{{ request('buscar') }}">
            <select name="estado" class="form-select w-auto">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <button type="submit" class="btn btn-dark px-4"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">ID Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: bold;">
                                        {{ substr($pedido->user->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $pedido->user->nombre_completo }}</div>
                                        <div class="text-muted small">{{ $pedido->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center fw-bold">{{ $pedido->total_formateado }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $pedido->estado_badge }}-subtle text-{{ $pedido->estado_badge }} rounded-pill text-uppercase">
                                    {{ str_replace('_', ' ', $pedido->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-3">
                                    <i class="bi bi-eye"></i> Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-3"></i>
                                No se encontraron pedidos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pedidos->hasPages())
    <div class="card-footer bg-white border-0 pt-4">
        {{ $pedidos->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
