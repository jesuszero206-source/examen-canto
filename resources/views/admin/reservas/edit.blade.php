@extends('layouts.admin')

@section('title', 'Editar Reserva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Editar Reserva: {{ $reserva->numero_reserva }}</h4>
    <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('admin.reservas.update', $reserva->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', $reserva->fecha->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Hora <span class="text-danger">*</span></label>
                    <input type="time" name="hora" class="form-control" required value="{{ old('hora', \Carbon\Carbon::parse($reserva->hora)->format('H:i')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Personas <span class="text-danger">*</span></label>
                    <input type="number" name="personas" class="form-control" required min="1" value="{{ old('personas', $reserva->personas) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" class="form-select" required>
                        @foreach($mesas as $mesa)
                            <option value="{{ $mesa->id }}" {{ old('mesa_id', $reserva->mesa_id) == $mesa->id ? 'selected' : '' }}>
                                Mesa {{ $mesa->numero }} (Cap: {{ $mesa->capacidad }} | {{ ucfirst($mesa->ubicacion) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select" required>
                        <option value="pendiente" {{ old('estado', $reserva->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ old('estado', $reserva->estado) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="finalizada" {{ old('estado', $reserva->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ old('estado', $reserva->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_reserva" class="form-control" required value="{{ old('nombre_reserva', $reserva->nombre_reserva) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                    <input type="tel" name="telefono" class="form-control" required value="{{ old('telefono', $reserva->telefono) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo', $reserva->correo) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $reserva->observaciones) }}</textarea>
                </div>
                
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> Actualizar Reserva
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
