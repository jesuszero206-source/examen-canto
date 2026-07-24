@props(['producto'])

<div class="card product-card h-100">
    <div class="card-img-wrapper position-relative">
        <a href="{{ route('producto.detalle', $producto->id) }}">
            <img src="{{ asset($producto->imagen ?? 'images/no-image.png') }}" class="card-img-top" alt="{{ $producto->nombre }}" loading="lazy">
        </a>
        
        @if($producto->existencia <= 0)
            <span class="badge bg-danger badge-stock">Agotado</span>
        @elseif($producto->existencia <= 5)
            <span class="badge bg-warning text-dark badge-stock">¡Solo quedan {{ $producto->existencia }}!</span>
        @endif
    </div>
    
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <a href="{{ route('producto.detalle', $producto->id) }}" class="text-decoration-none">
                <h5 class="card-title mb-0">{{ $producto->nombre }}</h5>
            </a>
            <span class="product-price">{{ $producto->precio_formateado }}</span>
        </div>
        
        <p class="card-text text-muted small mb-3 flex-grow-1">
            {{ Str::limit($producto->descripcion, 60) }}
        </p>

        @if($producto->total_calificaciones > 0)
            <div class="mb-3">
                <div class="d-flex align-items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($producto->promedio_calificacion))
                            <i class="bi bi-star-fill text-warning"></i>
                        @else
                            <i class="bi bi-star text-warning"></i>
                        @endif
                    @endfor
                    <span class="ms-1 fw-bold">{{ $producto->promedio_calificacion }}</span>
                    <span class="text-muted small ms-1">({{ $producto->total_calificaciones }} opiniones)</span>
                </div>
            </div>
        @else
            <div class="mb-3 text-muted small">
                Sin calificaciones aún
            </div>
        @endif
        
        <form action="{{ route('carrito.agregar') }}" method="POST" class="mt-auto form-add-to-cart">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
            <input type="hidden" name="cantidad" value="1">
            <button type="submit" class="btn btn-add-cart w-100 d-flex justify-content-center align-items-center gap-2" {{ $producto->existencia <= 0 ? 'disabled' : '' }}>
                <i class="bi bi-cart-plus"></i>
                <span class="btn-text">{{ $producto->existencia <= 0 ? 'Agotado' : 'Agregar' }}</span>
            </button>
        </form>

    </div>
</div>
