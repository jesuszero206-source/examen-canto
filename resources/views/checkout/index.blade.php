@extends('layouts.app')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="bg-cafe text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-credit-card fs-4"></i>
        </div>
        <h2 class="mb-0 fw-bold">Finalizar Compra</h2>
    </div>

    @if(!$carrito || $carrito->items->isEmpty())
        <div class="alert alert-warning">
            Tu carrito está vacío. <a href="{{ route('home') }}" class="alert-link">Vuelve al catálogo</a>.
        </div>
    @else
        <div class="row g-4">
            <!-- Formulario Checkout -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-person-check me-2"></i> Detalles del Cliente</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nombre Completo</label>
                                <div class="fs-5">{{ Auth::user()->nombre_completo }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Correo Electrónico</label>
                                <div class="fs-5">{{ Auth::user()->email ?? 'No registrado' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i> Pago y Notas</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Método de Pago Preferido *</label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="efectivo" required checked>
                                        <label class="btn btn-outline-secondary w-100 py-3 text-start d-flex flex-column" for="pago_efectivo">
                                            <i class="bi bi-cash fs-3 mb-2"></i>
                                            <span class="fw-bold">Efectivo</span>
                                            <small class="text-muted">Pagar en mostrador</small>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="metodo_pago" id="pago_tarjeta" value="tarjeta">
                                        <label class="btn btn-outline-secondary w-100 py-3 text-start d-flex flex-column" for="pago_tarjeta">
                                            <i class="bi bi-credit-card fs-3 mb-2"></i>
                                            <span class="fw-bold">Tarjeta</span>
                                            <small class="text-muted">Débito o crédito</small>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="metodo_pago" id="pago_transferencia" value="transferencia">
                                        <label class="btn btn-outline-secondary w-100 py-3 text-start d-flex flex-column" for="pago_transferencia">
                                            <i class="bi bi-phone fs-3 mb-2"></i>
                                            <span class="fw-bold">Transferencia</span>
                                            <small class="text-muted">App o código QR</small>
                                        </label>
                                    </div>
                                </div>
                                @error('metodo_pago')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="notas" class="form-label fw-bold">Notas del Pedido (Opcional)</label>
                                <textarea class="form-control bg-light border-0" id="notas" name="notas" rows="3" placeholder="Ej: Sin azúcar, para llevar, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Resumen y Confirmar -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-header bg-cafe text-white border-0 py-3 rounded-top-4">
                        <h5 class="fw-bold mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($carrito->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($item->producto->imagen ?? 'images/no-image.png') }}" class="rounded-3 object-fit-cover" width="45" height="45" alt="{{ $item->producto->nombre }}">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $item->producto->nombre }}</h6>
                                            <small class="text-muted">{{ $item->cantidad }} x ${{ number_format($item->producto->precio, 2) }}</small>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-success">${{ number_format($item->subtotal, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-light border-0 p-4 rounded-bottom-4">
                        @php
                            $subtotal = $carrito->total;
                            $impuesto = $subtotal * 0.16; // 16% IVA simulado si aplica
                            $total = $subtotal + $impuesto;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Impuestos (16%)</span>
                            <span class="fw-semibold">${{ number_format($impuesto, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5">Total a Pagar</span>
                            <span class="fw-bold fs-3 text-success">${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <button type="button" onclick="document.getElementById('checkout-form').submit();" class="btn btn-cafe-green w-100 py-3 fw-bold fs-5 shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Confirmar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
