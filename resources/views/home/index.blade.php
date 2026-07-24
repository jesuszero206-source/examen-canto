@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container fade-in-up">
        <h1 class="mb-3">El sabor perfecto para tu día</h1>
        <p class="mb-4 mx-auto" style="max-width: 600px;">Descubre nuestra selección de cafés de especialidad, postres artesanales y snacks preparados al momento.</p>
        
        <form action="{{ route('home') }}" method="GET" class="d-flex justify-content-center mx-auto" style="max-width: 500px;">
            <div class="input-group shadow-sm" style="border-radius: 30px; overflow: hidden;">
                <input type="text" class="form-control border-0 px-4 py-3" name="buscar" value="{{ $buscar }}" placeholder="¿Qué se te antoja hoy?">
                @if($categoriaActual)
                    <input type="hidden" name="categoria" value="{{ $categoriaActual }}">
                @endif
                <button class="btn bg-white border-0 px-4 text-cafe" type="submit">
                    <i class="bi bi-search fs-5"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Categorías & Catálogo -->
<section class="container py-4 mb-5">
    <div class="row g-4">
        <!-- Filtros Sidebar -->
        <div class="col-lg-3 fade-in-up-delay-1">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Categorías</h5>
                    <div class="d-flex flex-column gap-2 category-filters">
                        <a href="{{ route('home') }}{{ $buscar ? '?buscar='.$buscar : '' }}" 
                           class="btn btn-category text-start d-flex justify-content-between align-items-center {{ !$categoriaActual ? 'active' : '' }}">
                            <span><i class="bi bi-grid-fill me-2"></i> Todos</span>
                        </a>
                        
                        @foreach($categorias as $cat)
                            <a href="{{ route('home', ['categoria' => $cat->id]) }}{{ $buscar ? '&buscar='.$buscar : '' }}" 
                               class="btn btn-category text-start d-flex justify-content-between align-items-center {{ $categoriaActual == $cat->id ? 'active' : '' }}">
                                <span><i class="bi {{ $cat->icono ?? 'bi-cup' }} me-2"></i> {{ $cat->nombre }}</span>
                                <span class="badge bg-light text-dark rounded-pill">{{ $cat->productos_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grid de Productos -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up-delay-1">
                <h4 class="fw-bold mb-0">
                    @if($buscar)
                        Resultados para "{{ $buscar }}"
                    @elseif($categoriaActual)
                        {{ $categorias->where('id', $categoriaActual)->first()->nombre ?? 'Catálogo' }}
                    @else
                        Nuestro Catálogo
                    @endif
                </h4>
                <p class="text-muted mb-0 small">Mostrando {{ $productos->count() }} de {{ $productos->total() }} productos</p>
            </div>
            
            @if($productos->isEmpty())
                <div class="text-center py-5 fade-in-up-delay-2">
                    <div class="display-1 text-muted mb-3"><i class="bi bi-emoji-frown"></i></div>
                    <h3 class="text-cafe">No se encontraron productos</h3>
                    <p class="text-muted">Intenta cambiar tu búsqueda o categoría.</p>
                    <a href="{{ route('home') }}" class="btn btn-cafe mt-3">Ver todo el catálogo</a>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
                    @foreach($productos as $index => $producto)
                        <div class="col fade-in-up" style="animation-delay: {{ min(0.5, ($index % 6) * 0.1) }}s">
                            <x-product-card :producto="$producto" />
                        </div>
                    @endforeach
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center fade-in-up-delay-3">
                    {{ $productos->appends(['buscar' => $buscar, 'categoria' => $categoriaActual])->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
