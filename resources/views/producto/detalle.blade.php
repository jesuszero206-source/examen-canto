@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-cafe text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home', ['categoria' => $producto->categoria_id]) }}" class="text-cafe text-decoration-none">{{ $producto->categoria->nombre }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $producto->nombre }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="row g-0">
            <div class="col-md-5 bg-white d-flex align-items-center justify-content-center p-4">
                <img src="{{ asset($producto->imagen ?? 'images/no-image.png') }}" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 400px; object-fit: cover;" alt="{{ $producto->nombre }}">
            </div>
            
            <div class="col-md-7 d-flex flex-column">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-cafe-light text-cafe mb-2"><i class="bi {{ $producto->categoria->icono ?? 'bi-tag' }} me-1"></i> {{ $producto->categoria->nombre }}</span>
                        
                        @if($producto->existencia <= 0)
                            <span class="badge bg-danger">Agotado</span>
                        @elseif($producto->existencia <= 5)
                            <span class="badge bg-warning text-dark">¡Solo quedan {{ $producto->existencia }}!</span>
                        @else
                            <span class="badge bg-success">En Stock ({{ $producto->existencia }})</span>
                        @endif
                    </div>
                    
                    <h1 class="fw-bold mb-1">{{ $producto->nombre }}</h1>
                    <p class="text-muted small mb-4">Código: {{ $producto->codigo }}</p>
                    
                    <div class="product-price-detail mb-4">
                        {{ $producto->precio_formateado }}
                    </div>
                    
                    <h5 class="fw-bold">Descripción</h5>
                    <p class="text-muted mb-5">
                        {{ $producto->descripcion ?: 'No hay descripción disponible para este producto.' }}
                    </p>
                    
                    <form action="{{ route('carrito.agregar') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                        
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="cantidad" class="col-form-label fw-bold">Cantidad:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="cantidad" name="cantidad" class="form-control text-center" style="width: 80px;" value="1" min="1" max="{{ $producto->existencia }}" {{ $producto->existencia <= 0 ? 'disabled' : '' }}>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-cafe w-100 py-2 d-flex justify-content-center align-items-center gap-2" {{ $producto->existencia <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus fs-5"></i>
                                    {{ $producto->existencia <= 0 ? 'Producto Agotado' : 'Agregar al Carrito' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @if($relacionados->isNotEmpty())
        <div class="mt-5 pt-4 border-top">
            <h3 class="fw-bold mb-4 text-center">También te podría gustar</h3>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                @foreach($relacionados as $rel)
                    <div class="col">
                        <x-product-card :producto="$rel" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Inclusión del módulo de reseñas -->
    @include('producto.partials.resenas')
</div>
@endsection
