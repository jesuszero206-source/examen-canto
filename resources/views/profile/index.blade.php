@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-cafe text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                    {{ substr(Auth::user()->nombre, 0, 1) }}
                </div>
                <div>
                    <h2 class="mb-1">Mi Perfil</h2>
                    <p class="text-muted mb-0">Gestiona tu información personal y seguridad</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="bi bi-person-lines-fill text-cafe me-2"></i> Información Personal</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', Auth::user()->nombre) }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" name="apellido" value="{{ old('apellido', Auth::user()->apellido) }}">
                                @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Usuario</label>
                                <input type="text" class="form-control bg-light" value="{{ Auth::user()->username }}" readonly disabled>
                                <div class="form-text">El nombre de usuario no se puede cambiar.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', Auth::user()->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-cafe px-4">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="bi bi-shield-lock-fill text-cafe me-2"></i> Cambiar Contraseña</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Contraseña Actual</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nueva Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-cafe px-4">Actualizar Contraseña</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
