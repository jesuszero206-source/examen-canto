@extends('layouts.app')

@section('title', 'Mi Carrito')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="bg-cafe text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-cart3 fs-4"></i>
        </div>
        <h2 class="mb-0 fw-bold">Mi Carrito de Compras</h2>
    </div>

    @if(!$carrito || $carrito->items->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <div class="display-1 text-muted mb-4"><i class="bi bi-cart-x"></i></div>
                <h3 class="text-cafe fw-bold">Tu carrito está vacío</h3>
                <p class="text-muted mb-4">¡Agrega algunos deliciosos productos para continuar!</p>
                <a href="{{ route('home') }}" class="btn btn-cafe px-4 py-2">Explorar Catálogo</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Lista de Productos -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Productos ({{ $carrito->cantidad_total }})</h5>
                        <form action="{{ route('carrito.vaciar') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas vaciar el carrito?');">
                                <i class="bi bi-trash3 me-1"></i> Vaciar Carrito
                            </button>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table cart-table align-middle mb-0">
                            <thead class="text-uppercase small">
                                <tr>
                                    <th scope="col" colspan="2">Producto</th>
                                    <th scope="col" class="text-center">Precio</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-end">Total</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrito->items as $item)
                                    <tr>
                                        <td style="width: 80px;">
                                            <img src="{{ asset($item->producto->imagen ?? 'images/no-image.png') }}" class="cart-item-img shadow-sm" alt="{{ $item->producto->nombre }}">
                                        </td>
                                        <td>
                                            <a href="{{ route('producto.detalle', $item->producto_id) }}" class="text-decoration-none text-dark fw-bold d-block mb-1">
                                                {{ $item->producto->nombre }}
                                            </a>
                                            <span class="badge bg-cafe-light text-cafe">{{ $item->producto->categoria->nombre }}</span>
                                        </td>
                                        <td class="text-center fw-semibold text-muted">
                                            ${{ number_format($item->producto->precio, 2) }}
                                        </td>
                                        <td style="width: 140px;">
                                            <div class="d-flex align-items-center justify-content-center gap-2 bg-light rounded-pill p-1">
                                                <form action="{{ route('carrito.disminuir', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-qty btn-light rounded-circle shadow-sm">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                </form>
                                                <span class="fw-bold px-2">{{ $item->cantidad }}</span>
                                                <form action="{{ route('carrito.aumentar', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-qty btn-light rounded-circle shadow-sm" {{ $item->cantidad >= $item->producto->existencia ? 'disabled' : '' }}>
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            ${{ number_format($item->subtotal, 2) }}
                                        </td>
                                        <td class="text-end" style="width: 60px;">
                                            <form action="{{ route('carrito.eliminar', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <a href="{{ route('home') }}" class="btn btn-link text-cafe text-decoration-none mt-3">
                    <i class="bi bi-arrow-left me-1"></i> Continuar comprando
                </a>
            </div>
            
            <!-- Resumen -->
            <div class="col-lg-4">
                <div class="cart-summary shadow-sm sticky-top" style="top: 100px;">
                    <h5 class="fw-bold border-bottom pb-3 mb-3">Resumen de Compra</h5>
                    
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span>${{ number_format($carrito->total, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Impuestos (calculados en checkout)</span>
                        <span>$0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Total Estimado</span>
                        <span class="cart-total">${{ number_format($carrito->total, 2) }}</span>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="btn btn-cafe-green w-100 py-3 fw-bold fs-5 shadow-sm">
                        Proceder al Checkout <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    
                    <div class="mt-4 text-center">
                        <small class="text-muted d-block mb-2">Métodos de pago aceptados</small>
                        <div class="d-flex justify-content-center gap-2 fs-4 text-muted">
                            <i class="bi bi-cash"></i>
                            <i class="bi bi-credit-card"></i>
                            <i class="bi bi-phone"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
