@extends('layouts.admin')

@section('title', 'Reportes y Analíticas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h4 class="fw-bold mb-0">Dashboard de Reportes</h4>
    
    <!-- Filtros -->
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('admin.reportes.index') }}" method="GET" class="d-flex align-items-center gap-2" id="filterForm">
            <select name="filtro" class="form-select border-0 shadow-sm rounded-pill px-4" onchange="document.getElementById('filterForm').submit()">
                <option value="hoy" {{ $filtro == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $filtro == 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="mes" {{ $filtro == 'mes' ? 'selected' : '' }}>Este mes</option>
                <option value="30_dias" {{ $filtro == '30_dias' ? 'selected' : '' }}>Últimos 30 días</option>
                <option value="90_dias" {{ $filtro == '90_dias' ? 'selected' : '' }}>Últimos 90 días</option>
            </select>
        </form>

        <div class="dropdown">
            <button class="btn btn-cafe rounded-pill shadow-sm dropdown-toggle px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download me-2"></i> Exportar
            </button>
            <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item" target="_blank" href="{{ route('admin.reportes.exportar', ['formato' => 'pdf', 'filtro' => $filtro]) }}"><i class="bi bi-file-pdf text-danger me-2"></i> Exportar PDF</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reportes.exportar', ['formato' => 'csv', 'filtro' => $filtro]) }}"><i class="bi bi-file-earmark-excel text-success me-2"></i> Exportar Excel / CSV</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Tarjetas Superiores (8) -->
<div class="row g-3 mb-4">
    <!-- Ventas del Día -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Ventas Hoy</p>
                    <h5 class="fw-bold mb-0">${{ number_format($ventasDia, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Ventas del Mes -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Ventas Mes</p>
                    <h5 class="fw-bold mb-0">${{ number_format($ventasMes, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Ventas Totales -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Ventas Totales</p>
                    <h5 class="fw-bold mb-0">${{ number_format($ventasTotales, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Ticket Promedio -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Ticket Promedio</p>
                    <h5 class="fw-bold mb-0">${{ number_format($ticketPromedio, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Pedidos del Día -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Pedidos Hoy</p>
                    <h5 class="fw-bold mb-0">{{ $pedidosDia }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Pedidos del Mes -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-boxes fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1">Pedidos Mes</p>
                    <h5 class="fw-bold mb-0">{{ $pedidosMes }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Producto Estrella -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-muted small text-uppercase fw-bold mb-1">Prod. Estrella</p>
                    <h6 class="fw-bold mb-0 text-truncate" title="{{ $productoMasVendido?->nombre }}">{{ $productoMasVendido?->nombre ?? 'N/A' }}</h6>
                </div>
            </div>
        </div>
    </div>
    <!-- Cliente Estrella -->
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="bg-cafe bg-opacity-10 text-cafe rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-person-heart fs-4"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-muted small text-uppercase fw-bold mb-1">Top Cliente</p>
                    <h6 class="fw-bold mb-0 text-truncate" title="{{ $clienteMasCompras?->nombre }}">{{ $clienteMasCompras?->nombre ?? 'N/A' }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ESTADÍSTICAS DE RESERVAS -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <h5 class="fw-bold text-cafe"><i class="bi bi-calendar-check me-2"></i> Reportes de Reservas (Últimos 30 días)</h5>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-4">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Reservas Este Mes</p>
                <h4 class="fw-bold">{{ $reservasMesActual ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-danger border-4">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Reservas Canceladas</p>
                <h4 class="fw-bold text-danger">{{ $reservasCanceladasMes ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Mesas Más Populares</p>
                <ul class="list-unstyled mb-0 d-flex flex-wrap gap-2">
                    @forelse($mesasMasUtilizadas ?? [] as $mesa)
                        <li class="badge bg-cafe bg-opacity-10 text-cafe rounded-pill p-2 px-3">
                            Mesa {{ $mesa->numero }} ({{ $mesa->total_usos }} usos)
                        </li>
                    @empty
                        <li class="text-muted small">No hay datos suficientes</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart: Ventas Diarias -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Ventas Diarias ({{ ucfirst(str_replace('_', ' ', $filtro)) }})</h5>
            </div>
            <div class="card-body p-4">
                @if(count($chartVentasLabels) > 0)
                    <canvas id="ventasChart" height="120"></canvas>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart fs-1"></i>
                        <p class="mt-2">No hay datos de ventas en este periodo.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Chart: Ingresos por Categoría -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Ingresos por Categoría</h5>
            </div>
            <div class="card-body p-4 d-flex justify-content-center align-items-center">
                @if(count($chartCategoriasLabels) > 0)
                    <div style="width: 100%; max-width: 300px;">
                        <canvas id="categoriasChart"></canvas>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-pie-chart fs-1"></i>
                        <p class="mt-2">No hay ventas registradas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-award-fill text-warning me-2"></i> Productos Más Vendidos</h5>
        <span class="badge bg-light text-dark border">Top 10 en periodo: {{ ucfirst(str_replace('_', ' ', $filtro)) }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th class="text-center">Unidades Vendidas</th>
                        <th class="text-end pe-4">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProductos ?? [] as $index => $prod)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset($prod->imagen ?? 'images/no-image.png') }}" class="rounded-2" width="40" height="40" style="object-fit: cover;">
                                    <div class="fw-bold">{{ $prod->producto }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $prod->categoria }}</span>
                            </td>
                            <td class="text-center fs-5 fw-bold text-cafe">{{ $prod->unidades }}</td>
                            <td class="text-end pe-4 fw-bold text-success">${{ number_format($prod->ingresos, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No se registraron ventas de productos en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Gráfico de Ventas Diarias (Combinado: Line para Ingresos, Bar para Cantidad)
    const ventasLabels = {!! json_encode($chartVentasLabels ?? []) !!};
    const ventasData = {!! json_encode($chartVentasData ?? []) !!};
    const pedidosData = {!! json_encode($chartPedidosData ?? []) !!};
    
    if (document.getElementById('ventasChart') && ventasLabels.length > 0) {
        new Chart(document.getElementById('ventasChart'), {
            type: 'line',
            data: {
                labels: ventasLabels,
                datasets: [
                    {
                        label: 'Ingresos ($)',
                        data: ventasData,
                        borderColor: '#4d3b33', // text-cafe
                        backgroundColor: 'rgba(77, 59, 51, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Cant. de Pedidos',
                        data: pedidosData,
                        type: 'bar',
                        backgroundColor: 'rgba(131, 184, 158, 0.6)', // success/greenish
                        borderWidth: 0,
                        borderRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += '$' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y + ' pedidos';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Ingresos ($)' },
                        ticks: { callback: function(value) { return '$' + value; } }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Cantidad' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    }
    
    // Gráfico de Categorías (Doughnut)
    const categoriasLabels = {!! json_encode($chartCategoriasLabels ?? []) !!};
    const categoriasData = {!! json_encode($chartCategoriasData ?? []) !!};

    if (document.getElementById('categoriasChart') && categoriasLabels.length > 0) {
        new Chart(document.getElementById('categoriasChart'), {
            type: 'doughnut',
            data: {
                labels: categoriasLabels,
                datasets: [{
                    data: categoriasData,
                    backgroundColor: [
                        '#4d3b33', // Cafe
                        '#198754', // Green success
                        '#ffc107', // Warning
                        '#0dcaf0', // Info
                        '#dc3545', // Danger
                        '#6c757d', // Secondary
                        '#212529', // Dark
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const total = context.chart._metasets[context.datasetIndex].total;
                                const percentage = Math.round((val / total) * 100);
                                return ' $' + val.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
