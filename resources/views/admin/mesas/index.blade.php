@extends('layouts.admin')

@section('title', 'Gestión de Mesas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Gestión de Mesas</h4>
    <a href="{{ route('admin.mesas.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i> Nueva Mesa
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Mesa</th>
                        <th>Capacidad</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesas as $mesa)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $mesa->numero }}</div>
                                @if($mesa->nombre) <small class="text-muted">{{ $mesa->nombre }}</small> @endif
                            </td>
                            <td><i class="bi bi-people-fill text-cafe me-1"></i> {{ $mesa->capacidad }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($mesa->ubicacion) }}</span></td>
                            <td>
                                @php
                                    $color = match($mesa->estado) {
                                        'disponible' => 'success',
                                        'reservada' => 'warning text-dark',
                                        'ocupada' => 'danger',
                                        'limpieza' => 'info text-dark',
                                        'fuera_de_servicio' => 'dark',
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill">{{ ucfirst(str_replace('_', ' ', $mesa->estado)) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.mesas.edit', $mesa->id) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.mesas.destroy', $mesa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta mesa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4">No hay mesas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
