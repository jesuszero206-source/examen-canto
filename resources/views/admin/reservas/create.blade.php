@extends('layouts.admin')

@section('title', 'Nueva Reserva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Crear Reserva (Admin)</h4>
    <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('admin.reservas.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Hora <span class="text-danger">*</span></label>
                    <input type="time" name="hora" class="form-control" required value="{{ old('hora') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Personas <span class="text-danger">*</span></label>
                    <input type="number" name="personas" class="form-control" required min="1" value="{{ old('personas', 2) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" class="form-select" required>
                        <option value="">Seleccione una mesa...</option>
                        @foreach($mesas as $mesa)
                            <option value="{{ $mesa->id }}" {{ old('mesa_id') == $mesa->id ? 'selected' : '' }}>
                                Mesa {{ $mesa->numero }} (Cap: {{ $mesa->capacidad }} | {{ ucfirst($mesa->ubicacion) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select" required>
                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ old('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_reserva" class="form-control" required value="{{ old('nombre_reserva') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                    <input type="tel" name="telefono" class="form-control" required value="{{ old('telefono') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
                </div>
                
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> Crear Reserva
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
