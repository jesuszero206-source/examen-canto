@extends('layouts.admin')

@section('title', 'Editar Mesa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Editar Mesa: {{ $mesa->numero }}</h4>
    <a href="{{ route('admin.mesas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('admin.mesas.update', $mesa->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Número/Identificador de Mesa <span class="text-danger">*</span></label>
                    <input type="text" name="numero" class="form-control" required value="{{ old('numero', $mesa->numero) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre (Opcional)</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $mesa->nombre) }}" placeholder="Ej. Mesa Familiar">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                    <input type="number" name="capacidad" class="form-control" required min="1" value="{{ old('capacidad', $mesa->capacidad) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ubicación <span class="text-danger">*</span></label>
                    <select name="ubicacion" class="form-select" required>
                        <option value="interior" {{ $mesa->ubicacion == 'interior' ? 'selected' : '' }}>Interior</option>
                        <option value="terraza" {{ $mesa->ubicacion == 'terraza' ? 'selected' : '' }}>Terraza</option>
                        <option value="balcon" {{ $mesa->ubicacion == 'balcon' ? 'selected' : '' }}>Balcón</option>
                        <option value="jardin" {{ $mesa->ubicacion == 'jardin' ? 'selected' : '' }}>Jardín</option>
                        <option value="ventana" {{ $mesa->ubicacion == 'ventana' ? 'selected' : '' }}>Junto a Ventana</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select" required>
                        <option value="disponible" {{ $mesa->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="reservada" {{ $mesa->estado == 'reservada' ? 'selected' : '' }}>Reservada</option>
                        <option value="ocupada" {{ $mesa->estado == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                        <option value="limpieza" {{ $mesa->estado == 'limpieza' ? 'selected' : '' }}>En Limpieza</option>
                        <option value="fuera_de_servicio" {{ $mesa->estado == 'fuera_de_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Descripción (Opcional)</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $mesa->descripcion) }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" {{ $mesa->activa ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="activa">Mesa Activa (Visible para clientes)</label>
                    </div>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> Actualizar Mesa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
