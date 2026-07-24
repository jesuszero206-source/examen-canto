@extends('layouts.admin')

@section('title', 'Gestión de Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h4 class="fw-bold mb-0">Gestión de Reservas</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reservas.calendario') }}" class="btn btn-outline-cafe rounded-pill px-4 shadow-sm">
            <i class="bi bi-calendar3 me-2"></i> Calendario
        </a>
        <a href="{{ route('admin.reservas.plano') }}" class="btn btn-outline-cafe rounded-pill px-4 shadow-sm">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i> Plano de Mesas
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.reservas.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar folio o cliente..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada" {{ request('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-cafe w-100"><i class="bi bi-search me-2"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Folio</th>
                        <th>Cliente</th>
                        <th>Fecha y Hora</th>
                        <th>Personas / Ubicación</th>
                        <th>Estado / Mesa</th>
                        <th>Auditoría</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $reserva->numero_reserva }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $reserva->nombre_reserva }}</div>
                                <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $reserva->telefono }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-calendar-event text-cafe me-1"></i> {{ $reserva->fecha->format('d/m/Y') }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-people-fill me-1"></i> {{ $reserva->personas }} pers.</div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1">{{ ucfirst($reserva->ubicacion_preferida ?? 'Cualquiera') }}</span>
                            </td>
                            <td>
                                @php
                                    $color = match($reserva->estado) {
                                        'pendiente' => 'warning text-dark',
                                        'confirmada' => 'success',
                                        'rechazada' => 'danger',
                                        'finalizada' => 'info text-dark',
                                        'cancelada' => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill px-3">{{ ucfirst($reserva->estado) }}</span>
                                
                                @if($reserva->mesa)
                                    <div class="mt-1"><small class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Mesa {{ $reserva->mesa->numero }}</small></div>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted d-block">Creada: {{ $reserva->created_at->format('d/m/Y h:i A') }}</small>
                                @if($reserva->admin_id)
                                    <small class="text-info">Modificada: {{ $reserva->fecha_resolucion ? \Carbon\Carbon::parse($reserva->fecha_resolucion)->format('d/m/Y h:i A') : '' }}</small>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <!-- VER -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openVerModal({{ json_encode($reserva) }}, '{{ $reserva->mesa ? $reserva->mesa->numero : '' }}')" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    <!-- ACEPTAR -->
                                    @if($reserva->estado === 'pendiente')
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="openAceptarModal({{ $reserva->id }}, {{ $reserva->personas }}, '{{ $reserva->ubicacion_preferida }}')" title="Aceptar reserva">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    @endif

                                    <!-- RECHAZAR -->
                                    @if($reserva->estado === 'pendiente')
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="openRechazarModal({{ $reserva->id }})" title="Rechazar reserva">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @endif

                                    <!-- EDITAR -->
                                    <a href="{{ route('admin.reservas.edit', $reserva->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- CANCELAR -->
                                    @if(in_array($reserva->estado, ['confirmada']))
                                    <form action="{{ route('admin.reservas.cambiarEstado', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Cancelar esta reserva?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Cancelar">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- FINALIZAR -->
                                    @if(in_array($reserva->estado, ['confirmada']))
                                    <form action="{{ route('admin.reservas.cambiarEstado', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Finalizar esta reserva y liberar la mesa?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="estado" value="finalizada">
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Finalizar">
                                            <i class="bi bi-flag"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- ELIMINAR -->
                                    <form action="{{ route('admin.reservas.destroy', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta reserva? Se usará Soft Delete.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No se encontraron reservas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reservas->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $reservas->links() }}
    </div>
    @endif
</div>

<!-- Modal Aceptar -->
<div class="modal fade" id="modalAceptar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formAceptar" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="estado" value="confirmada">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i> Aceptar Reserva</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">La reserva tiene <strong id="acceptPersonas"></strong> personas. Ubicación preferida: <strong id="acceptUbicacion"></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar Mesa Disponible</label>
                        <select name="mesa_id" class="form-select" required>
                            <option value="">Seleccione una mesa...</option>
                            @foreach($mesas as $mesa)
                                <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} ({{ ucfirst($mesa->ubicacion) }} - Cap: {{ $mesa->capacidad }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Solo se muestran mesas activas en estado "disponible".</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar y Asignar Mesa</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formRechazar" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="estado" value="rechazada">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i> Rechazar Reserva</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo del Rechazo <span class="text-danger">*</span></label>
                        <textarea name="motivo_estado" class="form-control" rows="3" required placeholder="Ej: No hay disponibilidad en la fecha y hora seleccionada."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver -->
<div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Detalle de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush" id="detalleReserva">
                    <!-- Contenido renderizado por JS -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let modalAceptar, modalRechazar, modalVer;

    document.addEventListener('DOMContentLoaded', function() {
        modalAceptar = new bootstrap.Modal(document.getElementById('modalAceptar'));
        modalRechazar = new bootstrap.Modal(document.getElementById('modalRechazar'));
        modalVer = new bootstrap.Modal(document.getElementById('modalVer'));
    });

    function openAceptarModal(id, personas, ubicacion) {
        document.getElementById('formAceptar').action = `/admin/reservas/${id}/estado`;
        document.getElementById('acceptPersonas').textContent = personas;
        document.getElementById('acceptUbicacion').textContent = ubicacion || 'Cualquiera';
        modalAceptar.show();
    }

    function openRechazarModal(id) {
        document.getElementById('formRechazar').action = `/admin/reservas/${id}/estado`;
        modalRechazar.show();
    }

    function openVerModal(reserva, mesa_numero) {
        let html = `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Folio</span>
                <span class="fw-bold">${reserva.numero_reserva}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Cliente</span>
                <span>${reserva.nombre_reserva}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Teléfono</span>
                <span>${reserva.telefono}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Fecha</span>
                <span>${new Date(reserva.fecha).toLocaleDateString()}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Hora</span>
                <span>${reserva.hora}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Personas</span>
                <span>${reserva.personas}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Ubicación Preferida</span>
                <span class="text-capitalize">${reserva.ubicacion_preferida || 'Cualquiera'}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted">Mesa Asignada</span>
                <span class="fw-bold text-success">${mesa_numero ? '#' + mesa_numero : 'Sin asignar'}</span>
            </li>
            <li class="list-group-item">
                <span class="text-muted d-block mb-1">Observaciones</span>
                <p class="mb-0 small">${reserva.observaciones || 'Ninguna'}</p>
            </li>
        `;
        if (reserva.motivo_estado) {
            html += `
            <li class="list-group-item bg-danger bg-opacity-10">
                <span class="text-danger fw-bold d-block mb-1">Motivo del rechazo/cancelación</span>
                <p class="mb-0 small text-danger">${reserva.motivo_estado}</p>
            </li>`;
        }
        document.getElementById('detalleReserva').innerHTML = html;
        modalVer.show();
    }
</script>
@endpush
@endsection
