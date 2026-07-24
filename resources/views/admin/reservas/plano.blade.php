@extends('layouts.admin')

@section('title', 'Plano de Mesas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Plano Interactivo de Mesas</h4>
    <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Volver a Reservas
    </a>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-wrap gap-4 mb-5 justify-content-center">
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-success p-2 rounded-circle border border-2 border-white shadow-sm"> </span> Disponible</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-warning p-2 rounded-circle border border-2 border-white shadow-sm"> </span> Reservada (Hoy)</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-danger p-2 rounded-circle border border-2 border-white shadow-sm"> </span> Ocupada</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-info p-2 rounded-circle border border-2 border-white shadow-sm"> </span> En Limpieza</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-dark p-2 rounded-circle border border-2 border-white shadow-sm"> </span> Fuera de Servicio</div>
                </div>

                @php
                    $ubicaciones = $mesas->groupBy('ubicacion');
                @endphp

                @foreach($ubicaciones as $ubicacion => $mesasGrupo)
                    <h5 class="fw-bold text-uppercase text-muted mb-4 border-bottom pb-2">{{ $ubicacion }}</h5>
                    <div class="row g-4 mb-5">
                        @foreach($mesasGrupo as $mesa)
                            @php
                                $estadoReal = $mesa->estado;
                                // Si está disponible pero tiene una reserva activa hoy, mostramos que está reservada
                                if ($estadoReal === 'disponible' && $mesa->reservas->count() > 0) {
                                    $estadoReal = 'reservada';
                                }
                                
                                $color = match($estadoReal) {
                                    'disponible' => 'success',
                                    'reservada' => 'warning',
                                    'ocupada' => 'danger',
                                    'limpieza' => 'info',
                                    'fuera_de_servicio' => 'dark',
                                };
                            @endphp
                            <div class="col-sm-6 col-md-3 col-xl-2">
                                <div class="card bg-{{ $color }} bg-opacity-10 border border-{{ $color }} rounded-4 h-100 text-center p-3 cursor-pointer mesa-interactiva shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMesa{{ $mesa->id }}">
                                    <div class="position-relative">
                                        <i class="bi bi-display text-{{ $color }}" style="font-size: 3rem;"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-{{ $color }}">
                                            <i class="bi bi-people-fill"></i> {{ $mesa->capacidad }}
                                        </span>
                                    </div>
                                    <h6 class="fw-bold mt-2 text-{{ $color }} mb-0">Mesa {{ $mesa->numero }}</h6>
                                    
                                    @if($mesa->reservas->count() > 0)
                                        @php $primeraReserva = $mesa->reservas->first(); @endphp
                                        <small class="d-block mt-1 fw-bold text-dark" title="{{ $primeraReserva->nombre_reserva }}">
                                            <i class="bi bi-person"></i> {{ Str::limit($primeraReserva->nombre_reserva, 12) }}
                                        </small>
                                        <small class="d-block text-muted">
                                            <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::parse($primeraReserva->hora)->format('H:i') }}
                                        </small>
                                    @else
                                        <small class="d-block mt-1 fw-bold text-muted">Disponible</small>
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">Sin reserva programada</small>
                                    @endif
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="modalMesa{{ $mesa->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Mesa {{ $mesa->numero }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted mb-4">{{ $mesa->descripcion ?? 'Sin descripción.' }}</p>
                                            
                                            <ul class="list-group list-group-flush mb-4 rounded-3 shadow-sm border">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Ubicación
                                                    <span class="fw-bold">{{ ucfirst($mesa->ubicacion) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Capacidad
                                                    <span class="fw-bold">{{ $mesa->capacidad }} personas</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Estado Físico
                                                    <span class="badge bg-{{ $color }} rounded-pill">{{ ucfirst(str_replace('_', ' ', $mesa->estado)) }}</span>
                                                </li>
                                            </ul>

                                            @if($mesa->reservas->count() > 0)
                                                <h6 class="fw-bold mb-3"><i class="bi bi-calendar-check text-success me-2"></i>Próximas Reservas (Hoy)</h6>
                                                <div class="list-group">
                                                    @foreach($mesa->reservas as $reserva)
                                                        <a href="{{ route('admin.reservas.index', ['buscar' => $reserva->numero_reserva]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-start-0 border-end-0 border-success border-3">
                                                            <div>
                                                                <div class="fw-bold">{{ $reserva->nombre_reserva }}</div>
                                                                <small class="text-muted">{{ $reserva->personas }} personas | Tel: {{ $reserva->telefono }}</small>
                                                            </div>
                                                            <span class="badge bg-success rounded-pill">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-light text-center border text-muted">
                                                    No hay reservas programadas para hoy.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.mesa-interactiva { transition: all 0.2s ease; }
.mesa-interactiva:hover { transform: translateY(-5px); filter: brightness(0.95); }
</style>
@endsection
