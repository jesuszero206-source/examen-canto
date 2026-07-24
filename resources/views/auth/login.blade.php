@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-page">
    <div class="container py-5">
        <div class="card login-card">
            <div class="card-header">
                <h3>Bienvenido</h3>
                <p>Ingresa a tu cuenta de Café Aurora</p>
            </div>
            
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="username" class="form-label fw-bold">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-login text-white">
                            Iniciar Sesión <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        
                        <a href="{{ route('home') }}" class="btn btn-guest text-center">
                            Continuar como Invitado
                        </a>
                    </div>
                </form>
                
                <div class="mt-4 text-center">
                    <p class="text-muted small">
                        ¿Demo? Usa <strong>admin</strong> / <strong>admin123</strong> o <strong>cliente</strong> / <strong>1234</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
