@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-cafe"><i class="bi bi-journal-bookmark-fill me-2"></i>Mis Reservas</h2>
        <a href="{{ route('reservas.index') }}" class="btn btn-cafe rounded-pill shadow-sm">Nueva Reserva</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($reservas as $reserva)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 reserva-card overflow-hidden">
                    <div class="card-header border-0 
                        @if($reserva->estado == 'pendiente') bg-warning text-dark
                        @elseif($reserva->estado == 'confirmada') bg-success text-white
                        @elseif($reserva->estado == 'rechazada') bg-danger text-white
                        @elseif($reserva->estado == 'cancelada') bg-secondary text-white
                        @elseif($reserva->estado == 'finalizada') bg-info text-white
                        @endif p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">
                            @if($reserva->estado == 'pendiente') <i class="bi bi-clock-history me-1"></i> Pendiente
                            @elseif($reserva->estado == 'confirmada') <i class="bi bi-check-circle-fill me-1"></i> Confirmada
                            @elseif($reserva->estado == 'rechazada') <i class="bi bi-x-circle-fill me-1"></i> Rechazada
                            @elseif($reserva->estado == 'cancelada') <i class="bi bi-slash-circle-fill me-1"></i> Cancelada
                            @elseif($reserva->estado == 'finalizada') <i class="bi bi-flag-fill me-1"></i> Finalizada
                            @endif
                        </span>
                        <span class="small opacity-75">#{{ $reserva->numero_reserva }}</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        
                        <div class="mb-3 text-center">
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Fecha y Hora</p>
                            <h5 class="fw-bold text-dark">{{ $reserva->fecha->format('d/m/Y') }} a las {{ $reserva->hora->format('h:i A') }}</h5>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span class="text-muted"><i class="bi bi-people-fill me-1"></i> Personas</span>
                            <span class="fw-bold">{{ $reserva->personas }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                            <span class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i> Ubicación</span>
                            <span class="fw-bold text-capitalize">{{ $reserva->ubicacion_preferida ?? 'Cualquiera' }}</span>
                        </div>

                        @if($reserva->mesa_id && $reserva->mesa)
                            <div class="alert alert-light border border-2 border-success border-opacity-25 rounded-3 py-2 px-3 mb-3 text-center">
                                <span class="d-block small text-muted mb-1">Mesa Asignada</span>
                                <strong class="fs-5 text-success">#{{ $reserva->mesa->numero }}</strong>
                            </div>
                        @endif

                        <div class="alert 
                            @if($reserva->estado == 'pendiente') alert-warning
                            @elseif($reserva->estado == 'confirmada') alert-success
                            @elseif($reserva->estado == 'rechazada') alert-danger
                            @elseif($reserva->estado == 'cancelada') alert-secondary
                            @elseif($reserva->estado == 'finalizada') alert-info
                            @endif 
                            rounded-3 py-2 px-3 small flex-grow-1 d-flex flex-column justify-content-center">
                            
                            @if($reserva->estado == 'pendiente')
                                Estamos revisando tu solicitud.
                            @elseif($reserva->estado == 'confirmada')
                                Tu reserva ha sido confirmada. Te esperamos en Café Aurora.
                            @elseif($reserva->estado == 'rechazada')
                                <strong>Motivo:</strong> {{ $reserva->motivo_estado ?? 'No especificado' }}<br>
                                <a href="{{ route('reservas.index') }}" class="btn btn-outline-danger btn-sm mt-2 rounded-pill">Crear otra reserva</a>
                            @elseif($reserva->estado == 'cancelada')
                                Reserva cancelada.
                            @elseif($reserva->estado == 'finalizada')
                                Gracias por visitarnos.
                            @endif
                        </div>

                        <div class="text-muted small mb-3 text-center">
                            Creada el {{ $reserva->created_at->format('d/m/Y h:i A') }}
                        </div>

                        @if(in_array($reserva->estado, ['pendiente', 'confirmada']) && now()->diffInHours(\Carbon\Carbon::parse($reserva->fecha->format('Y-m-d') . ' ' . $reserva->hora->format('H:i:s')), false) >= 2)
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 mt-auto" onclick="confirmarCancelacion({{ $reserva->id }})">
                                Cancelar Reserva
                            </button>
                            <form id="cancel-form-{{ $reserva->id }}" action="{{ route('perfil.reservas.cancelar', $reserva->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('PUT')
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">Aún no tienes reservas</h4>
                <p class="text-muted">Anímate y aparta tu lugar en nuestra cafetería.</p>
                <a href="{{ route('reservas.index') }}" class="btn btn-cafe rounded-pill mt-2">Reservar ahora</a>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function confirmarCancelacion(id) {
    if(confirm('¿Estás seguro de que deseas cancelar esta reserva? Esta acción no se puede deshacer.')) {
        document.getElementById('cancel-form-' + id).submit();
    }
}
</script>
<style>
.reserva-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.reserva-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>
@endpush
@endsection
