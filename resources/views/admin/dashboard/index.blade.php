@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- TARJETA 1: PEDIDOS TOTALES -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalPedidos ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Pedidos Registrados</p>
            </div>
        </div>
    </div>
    
    <!-- TARJETA 2: INGRESOS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" title="Ingresos Hoy: ${{ number_format($ingresosHoy ?? 0, 2) }} | Mes: ${{ number_format($ingresosMes ?? 0, 2) }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill">Completados</span>
                </div>
                <h3 class="fw-bold mb-1">${{ number_format($ingresosTotales ?? 0, 2) }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Ingresos Totales</p>
            </div>
        </div>
    </div>
    
    <!-- TARJETA 3: PRODUCTOS ACTIVOS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalProductos ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Productos Activos</p>
            </div>
        </div>
    </div>
    
    <!-- TARJETA 4: USUARIOS REGISTRADOS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-dark rounded-pill">Clientes</span>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalUsuarios ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Usuarios Registrados</p>
            </div>
        </div>
    </div>
</div>

<!-- TARJETAS DE RESERVAS -->
<div class="row g-4 mb-4">
    <!-- Reservas del día -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-cafe bg-opacity-10 text-cafe rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $reservasHoy ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Reservas de Hoy</p>
            </div>
        </div>
    </div>
    
    <!-- Reservas Pendientes -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $reservasPendientes ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Pendientes</p>
            </div>
        </div>
    </div>

    <!-- Reservas Confirmadas -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $reservasConfirmadas ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Confirmadas</p>
            </div>
        </div>
    </div>

    <!-- Reservas Rechazadas -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-x-circle fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $reservasRechazadas ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Rechazadas</p>
            </div>
        </div>
    </div>
</div>

<!-- TARJETAS DE MESAS -->
<div class="row g-4 mb-4">
    <!-- Mesas Disponibles -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-grid-3x3-gap fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ $mesasDisponibles ?? 0 }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Mesas Disponibles / {{ $mesasTotales ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Mesas Ocupadas y Reservadas -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-workspace fs-4"></i>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <span class="badge bg-danger-subtle text-danger rounded-pill mb-1">{{ $mesasOcupadas ?? 0 }} ocupadas</span>
                        <span class="badge bg-warning-subtle text-dark rounded-pill">{{ $mesasReservadas ?? 0 }} reservadas</span>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ ($mesasOcupadas ?? 0) + ($mesasReservadas ?? 0) }}</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Mesas No Disponibles</p>
            </div>
        </div>
    </div>

    <!-- Ocupación -->
    <div class="col-12 col-sm-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pie-chart fs-4"></i>
                    </div>
                </div>
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar {{ ($porcentajeOcupacion ?? 0) > 80 ? 'bg-danger' : 'bg-info' }}" role="progressbar" style="width: {{ $porcentajeOcupacion ?? 0 }}%" aria-valuenow="{{ $porcentajeOcupacion ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $porcentajeOcupacion ?? 0 }}%</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Ocupación Actual</p>
            </div>
        </div>
    </div>
</div>
<!-- ESTADÍSTICAS ADICIONALES -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 text-secondary"><i class="bi bi-graph-up me-2"></i> Estadísticas Generales</h5>
                <div class="row text-center g-3">
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Ventas Hoy</p>
                        <h5 class="fw-bold text-success">${{ number_format($ingresosHoy ?? 0, 2) }}</h5>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Ventas del Mes</p>
                        <h5 class="fw-bold text-primary">${{ number_format($ingresosMes ?? 0, 2) }}</h5>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Promedio por Pedido</p>
                        <h5 class="fw-bold">${{ number_format($promedioPedido ?? 0, 2) }}</h5>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Productos Vendidos</p>
                        <h5 class="fw-bold text-info">{{ $totalProductosVendidos ?? 0 }}</h5>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Producto Estrella</p>
                        <h5 class="fw-bold text-truncate" title="{{ $productoMasVendido?->producto->nombre ?? 'N/A' }}">{{ $productoMasVendido?->producto->nombre ?? 'N/A' }}</h5>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Cliente Estrella</p>
                        <h5 class="fw-bold text-truncate" title="{{ $clienteEstrella?->user->nombre_completo ?? 'N/A' }}">{{ $clienteEstrella?->user->nombre ?? 'N/A' }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- ALERTAS DE STOCK BAJO -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4 border-top border-warning border-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#stockBajoPanel" aria-expanded="false" aria-controls="stockBajoPanel">
                <h5 class="fw-bold mb-0 text-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Stock Bajo
                </h5>
                <div>
                    @if(isset($stockBajo) && $stockBajo->count() > 0)
                        <span class="badge bg-danger rounded-pill shadow-sm me-2">{{ $stockBajo->count() }} alertas</span>
                    @else
                        <span class="badge bg-success rounded-pill shadow-sm me-2"><i class="bi bi-check"></i></span>
                    @endif
                    <i class="bi bi-chevron-down text-muted"></i>
                </div>
            </div>
            
            <div id="stockBajoPanel" class="collapse">
                <div class="card-body p-4 pt-0">
                    @if(isset($stockBajo) && $stockBajo->count() > 0)
                        <div class="row g-3 mt-2">
                            @foreach($stockBajo as $prod)
                                <div class="col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset($prod->imagen ?? 'images/no-image.png') }}" class="rounded-2" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 150px;">{{ $prod->nombre }}</h6>
                                                <small class="text-muted">Mínimo: 5</small>
                                            </div>
                                        </div>
                                        <span class="badge {{ $prod->existencia == 0 ? 'bg-danger' : 'bg-warning text-dark' }} fs-6" title="Stock actual">
                                            {{ $prod->existencia }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                            <p class="mt-3 mb-0 text-muted fw-bold">No hay problemas de stock</p>
                        </div>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-warning px-5 fw-bold rounded-pill">Ir a Inventario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ÚLTIMOS PEDIDOS -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Últimos Pedidos</h5>
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-sm btn-link text-decoration-none">Ver todos</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle" style="min-width: 800px;">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Método</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPedidos ?? [] as $pedido)
                                <tr>
                                    <td class="fw-bold">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $pedido->user->nombre_completo ?? 'Usuario Eliminado' }}</td>
                                    <td>{{ $pedido->created_at->format('d M, Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm dropdown-toggle rounded-pill px-3 fw-bold bg-{{ $pedido->estado_badge }} bg-opacity-10 text-{{ $pedido->estado_badge == 'warning' ? 'dark' : $pedido->estado_badge }}" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid rgba(var(--bs-{{ $pedido->estado_badge }}-rgb), 0.2);">
                                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                            </button>
                                            <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                                <li>
                                                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="estado" value="pendiente">
                                                        <button class="dropdown-item fw-medium small {{ $pedido->estado == 'pendiente' ? 'active' : '' }}"><i class="bi bi-circle-fill text-warning me-2" style="font-size: 0.5rem;"></i> Pendiente</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="estado" value="en_proceso">
                                                        <button class="dropdown-item fw-medium small {{ $pedido->estado == 'en_proceso' ? 'active' : '' }}"><i class="bi bi-circle-fill text-info me-2" style="font-size: 0.5rem;"></i> Preparando</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="estado" value="completado">
                                                        <button class="dropdown-item fw-medium small {{ $pedido->estado == 'completado' ? 'active' : '' }}"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.5rem;"></i> Entregado</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="estado" value="cancelado">
                                                        <button class="dropdown-item fw-medium small text-danger {{ $pedido->estado == 'cancelado' ? 'active' : '' }}"><i class="bi bi-x-circle-fill me-2" style="font-size: 0.7rem;"></i> Cancelado</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-capitalize text-muted small"><i class="bi bi-wallet2 me-1"></i> {{ $pedido->metodo_pago }}</span>
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($pedido->total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-light border rounded-3 text-primary" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    

@endsection
