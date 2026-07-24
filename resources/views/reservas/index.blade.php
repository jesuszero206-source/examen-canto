@extends('layouts.app')

@section('title', 'Reserva tu Mesa')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5 animate__animated animate__fadeInDown">
                <h1 class="fw-bold text-cafe mb-3"><i class="bi bi-calendar-check text-success me-2"></i> Reserva tu Mesa</h1>
                <p class="text-muted">Asegura tu lugar en Café Aurora y disfruta de la mejor experiencia.</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-header bg-cafe text-white p-4 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i> Detalles de tu Reserva</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('error'))
                        <div class="alert alert-danger rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('reservas.store') }}" method="POST" id="formReserva">
                        @csrf
                        
                        <div class="row g-4 mb-4 pb-4 border-bottom">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" class="form-control form-control-lg bg-light border-0" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Hora <span class="text-danger">*</span></label>
                                <input type="time" name="hora" id="hora" class="form-control form-control-lg bg-light border-0" required min="07:00" max="22:00">
                                <small class="text-muted">Horario: 7:00 AM - 10:00 PM</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Personas <span class="text-danger">*</span></label>
                                <input type="number" name="personas" id="personas" class="form-control form-control-lg bg-light border-0" required min="1" max="20" value="2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Ubicación Deseada</label>
                                <select name="ubicacion_preferida" id="ubicacion_preferida" class="form-select form-select-lg bg-light border-0">
                                    <option value="">Cualquier ubicación</option>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi }}">{{ ucfirst($ubi) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="datosContacto">
                            <h5 class="fw-bold text-cafe mb-3">Tus Datos</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_reserva" class="form-control form-control-lg bg-light border-0" required value="{{ Auth::check() ? Auth::user()->nombre_completo : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Teléfono <span class="text-danger">*</span></label>
                                    <input type="tel" name="telefono" class="form-control form-control-lg bg-light border-0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Correo Electrónico</label>
                                    <input type="email" name="correo" class="form-control form-control-lg bg-light border-0" value="{{ Auth::check() ? Auth::user()->email : '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Observaciones (Opcional)</label>
                                    <textarea name="observaciones" class="form-control bg-light border-0" rows="3" placeholder="Ej. Celebración de cumpleaños, requerimientos especiales..."></textarea>
                                </div>
                                <div class="col-12 mt-4 text-center">
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 py-3 shadow-lg fs-5 w-100 fw-bold">
                                        SOLICITAR RESERVA <i class="bi bi-calendar-plus ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.transition-all { transition: all 0.3s ease; }
</style>
@endpush
