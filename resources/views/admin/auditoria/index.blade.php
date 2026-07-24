@extends('layouts.admin')

@section('title', 'Log de Auditoría')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Registro de Actividad</h5>
        <span class="badge bg-light text-muted border">Solo lectura</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="bg-light text-muted text-uppercase">
                    <tr>
                        <th class="ps-4">Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla / Ref</th>
                        <th>IP Address</th>
                        <th class="text-end pe-4">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                        <tr>
                            <td class="ps-4 text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->usuario->username ?? 'Sistema' }}</td>
                            <td>
                                @php
                                    $color = 'secondary';
                                    if(str_contains(strtolower($log->accion), 'crea') || str_contains(strtolower($log->accion), 'agreg')) $color = 'success';
                                    if(str_contains(strtolower($log->accion), 'actualiza') || str_contains(strtolower($log->accion), 'edit')) $color = 'info';
                                    if(str_contains(strtolower($log->accion), 'elimin') || str_contains(strtolower($log->accion), 'borra')) $color = 'danger';
                                    if(str_contains(strtolower($log->accion), 'login') || str_contains(strtolower($log->accion), 'logout')) $color = 'dark';
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill">{{ $log->accion }}</span>
                            </td>
                            <td>
                                <span class="text-cafe fw-semibold">{{ $log->tabla }}</span>
                                @if($log->registro_id) <span class="text-muted">#{{ $log->registro_id }}</span> @endif
                            </td>
                            <td class="text-muted small">{{ $log->ip_address }}</td>
                            <td class="text-end pe-4">
                                @if($log->valores_anteriores || $log->valores_nuevos)
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                        Ver JSON
                                    </button>
                                    
                                    <!-- Modal JSON -->
                                    <div class="modal fade text-start" id="logModal{{ $log->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Detalles de Auditoría</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-4">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-muted text-uppercase small">Valores Anteriores</h6>
                                                            <pre class="bg-light p-3 rounded border text-danger" style="max-height: 400px; overflow-y: auto;">{{ $log->valores_anteriores ? json_encode($log->valores_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'null' }}</pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-muted text-uppercase small">Valores Nuevos</h6>
                                                            <pre class="bg-light p-3 rounded border text-success" style="max-height: 400px; overflow-y: auto;">{{ $log->valores_nuevos ? json_encode($log->valores_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'null' }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Sin datos</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay registros de auditoría.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(isset($logs) && method_exists($logs, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
