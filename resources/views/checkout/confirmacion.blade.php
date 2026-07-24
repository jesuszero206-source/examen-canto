@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <!-- Mensaje de Éxito Principal -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3 shadow-sm" style="width: 100px; height: 100px;">
                    <i class="bi bi-check-lg" style="font-size: 3.5rem;"></i>
                </div>
                <h1 class="fw-bold text-dark mb-2">¡Gracias por tu compra!</h1>
                <p class="text-muted fs-5">Tu pedido ha sido confirmado y ya estamos trabajando en él.</p>
            </div>

            <!-- Recibo Digital -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative">
                <!-- Decoración superior (estilo ticket) -->
                <div class="bg-cafe text-white p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-white-50 text-uppercase fw-bold tracking-wide">Recibo de Orden</small>
                        <h4 class="mb-0 fw-bold mt-1">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</h4>
                    </div>
                    <div class="text-end">
                        <small class="text-white-50 text-uppercase fw-bold tracking-wide">Monto Pagado</small>
                        <h4 class="mb-0 fw-bold mt-1">${{ number_format($pedido->total, 2) }}</h4>
                    </div>
                </div>

                <div class="card-body p-5" id="recibo-pdf">
                    
                    <!-- Tracker de estado de la orden -->
                    @php
                        $progress = 25;
                        $btn1 = 'success'; $text1 = 'success'; $icon1 = '<i class="bi bi-check"></i>';
                        $btn2 = 'light border'; $text2 = 'muted fw-bold'; $icon2 = '2';
                        $btn3 = 'light border'; $text3 = 'muted fw-bold'; $icon3 = '3';
                        
                        if($pedido->estado == 'en_proceso') {
                            $progress = 50;
                            $btn2 = 'success'; $text2 = 'success'; $icon2 = '<i class="bi bi-check"></i>';
                        } elseif($pedido->estado == 'completado') {
                            $progress = 100;
                            $btn2 = 'success'; $text2 = 'success'; $icon2 = '<i class="bi bi-check"></i>';
                            $btn3 = 'success'; $text3 = 'success'; $icon3 = '<i class="bi bi-check"></i>';
                        } elseif($pedido->estado == 'cancelado') {
                            $progress = 100;
                            $btn1 = 'danger'; $text1 = 'danger'; $icon1 = '<i class="bi bi-x"></i>';
                            $btn2 = 'danger'; $text2 = 'danger'; $icon2 = '<i class="bi bi-x"></i>';
                            $btn3 = 'danger'; $text3 = 'danger'; $icon3 = '<i class="bi bi-x"></i>';
                        }
                    @endphp
                    <div class="row justify-content-center mb-5 pb-3 border-bottom" data-html2canvas-ignore="true">
                        <div class="col-10">
                            <div class="position-relative m-4">
                                <div class="progress" style="height: 4px;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-{{ $pedido->estado == 'cancelado' ? 'danger' : 'success' }}" style="width: {{ $progress }}%;"></div>
                                </div>
                                <button type="button" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-{{ $btn1 }} rounded-pill" style="width: 2rem; height:2rem;">{!! $icon1 !!}</button>
                                <button type="button" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-{{ $btn2 }} rounded-pill text-{{ $text2 }}" style="width: 2rem; height:2rem;">{!! $icon2 !!}</button>
                                <button type="button" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-{{ $btn3 }} rounded-pill text-{{ $text3 }}" style="width: 2rem; height:2rem;">{!! $icon3 !!}</button>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="fw-bold text-{{ $text1 }}">{{ $pedido->estado == 'cancelado' ? 'Cancelado' : 'Confirmado' }}</small>
                                <small class="text-{{ $text2 }} ms-3 fw-medium">Preparando</small>
                                <small class="text-{{ $text3 }} fw-medium">Entregado</small>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del cliente e Info -->
                    <div class="row g-4 mb-5">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light border rounded-4 h-100">
                                <h6 class="text-muted text-uppercase fw-bold small mb-3">Información del Cliente</h6>
                                <p class="mb-1 fw-bold text-dark">{{ $pedido->user->nombre_completo ?? Auth::user()->nombre_completo }}</p>
                                <p class="mb-1 text-muted small"><i class="bi bi-envelope me-2"></i> {{ $pedido->user->email ?? Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light border rounded-4 h-100">
                                <h6 class="text-muted text-uppercase fw-bold small mb-3">Detalles de la Transacción</h6>
                                <p class="mb-2 text-muted small d-flex justify-content-between align-items-center">
                                    <span>Fecha de Compra:</span>
                                    <span class="fw-bold text-dark">{{ $pedido->created_at->format('d/m/Y h:i A') }}</span>
                                </p>
                                <p class="mb-0 text-muted small d-flex justify-content-between align-items-center">
                                    <span>Método de Pago:</span>
                                    <span class="fw-bold text-dark text-capitalize bg-white px-2 py-1 rounded shadow-sm border">
                                        @if($pedido->metodo_pago == 'efectivo')
                                            <i class="bi bi-cash text-success me-1"></i> Efectivo
                                        @elseif($pedido->metodo_pago == 'tarjeta')
                                            <i class="bi bi-credit-card text-primary me-1"></i> Tarjeta
                                        @else
                                            <i class="bi bi-bank text-info me-1"></i> Transferencia
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Código QR si no es efectivo -->
                    @if($pedido->metodo_pago != 'efectivo')
                    <div class="row justify-content-center mb-5 text-center">
                        <div class="col-md-6 border rounded-4 p-4 bg-white shadow-sm">
                            <h6 class="text-muted text-uppercase fw-bold small mb-3">Código de Referencia de Pago</h6>
                            @php
                                $banco = "Banco Aurora";
                                $clabe = "012345678901234567";
                                $beneficiario = "Café Aurora SA de CV";
                                $monto = number_format($pedido->total, 2, '.', '');
                                $concepto = "Pedido " . str_pad($pedido->id, 5, '0', STR_PAD_LEFT);
                                
                                $datosQR = "DATOS DE PAGO\nBanco: $banco\nCLABE: $clabe\nBeneficiario: $beneficiario\nMonto: $$monto\nConcepto: $concepto";
                                $qrData = urlencode($datosQR);
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qrData }}" alt="QR Code" class="img-fluid mb-2">
                            <p class="small text-muted mb-0">Escanea este código con la cámara de tu celular para obtener los datos bancarios y realizar el pago.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Tabla de productos comprados -->
                    <h6 class="text-muted text-uppercase fw-bold small mb-3">Resumen de Artículos</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="border-bottom">
                                <tr class="text-muted small text-uppercase fw-bold">
                                    <th class="ps-0 pb-3">Producto</th>
                                    <th class="text-center pb-3">Cant.</th>
                                    <th class="text-end pb-3">Precio</th>
                                    <th class="text-end pe-0 pb-3">Total</th>
                                </tr>
                            </thead>
                            <tbody class="border-bottom">
                                @foreach($pedido->detalles as $detalle)
                                    <tr>
                                        <td class="ps-0 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-cafe-light text-cafe rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                                    <i class="bi {{ $detalle->producto->categoria->icono ?? 'bi-cup-hot' }} fs-4"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">{{ $detalle->producto->nombre }}</span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal mt-1">{{ $detalle->producto->categoria->nombre ?? 'Bebida' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 fw-bold text-dark">{{ $detalle->cantidad }}</td>
                                        <td class="text-end py-3 text-muted">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end pe-0 py-3 fw-bold text-dark">${{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cálculos Finales -->
                    <div class="row justify-content-end">
                        <div class="col-md-6 col-lg-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-medium">Subtotal</span>
                                <span class="fw-bold text-dark">${{ number_format($pedido->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="text-muted fw-medium">Impuesto (16% IVA)</span>
                                <span class="fw-bold text-dark">${{ number_format($pedido->impuesto, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border">
                                <span class="fs-5 fw-bold text-dark">Total Pagado</span>
                                <span class="fs-4 fw-bold text-success">${{ number_format($pedido->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción (estético) -->
                <div class="card-footer bg-white border-top p-4" data-html2canvas-ignore="true">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">
                            <i class="bi bi-arrow-left me-2"></i> Volver a la Tienda
                        </a>
                        <div class="d-flex gap-2">
                            <button class="btn btn-light border rounded-pill px-3 fw-bold shadow-sm text-secondary hover-bg-light" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i> Imprimir
                            </button>
                            <button id="btn-download-pdf" class="btn btn-cafe rounded-pill px-4 shadow-sm fw-bold text-white border-0">
                                <i class="bi bi-download me-2"></i> Guardar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('btn-download-pdf').addEventListener('click', function() {
        const element = document.querySelector('.card');
        const opt = {
            margin:       0.5,
            filename:     'Recibo_Pedido_{{ $pedido->id }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    });
</script>

<style>
    /* Estilos personalizados para la vista de recibo */
    .tracking-wide { letter-spacing: 1.5px; }
    .bg-cafe { background-color: #5c3a21; }
    .bg-cafe-light { background-color: #f7ede2; border: 1px solid #ebd5c1; }
    .text-cafe { color: #8B4513; }
    .btn-cafe { background-color: #8B4513; transition: all 0.2s ease; }
    .btn-cafe:hover { background-color: #6b340e; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139, 69, 19, 0.2) !important; }
    .hover-bg-light:hover { background-color: #e9ecef !important; }
    
    @media print {
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; margin: 0; }
        .card-footer, .btn, .progress, .rounded-pill { display: none !important; }
    }
</style>
@endsection
