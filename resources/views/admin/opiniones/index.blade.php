@extends('layouts.admin')

@section('title', 'Moderación de Opiniones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Moderación de Opiniones</h1>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.opiniones.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Estado</label>
                <select name="estado" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="visible" {{ request('estado') == 'visible' ? 'selected' : '' }}>Visibles</option>
                    <option value="oculto" {{ request('estado') == 'oculto' ? 'selected' : '' }}>Ocultos</option>
                    <option value="reportado" {{ request('estado') == 'reportado' ? 'selected' : '' }}>Reportados</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Producto</label>
                <select name="producto_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los productos</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}" {{ request('producto_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="{{ route('admin.opiniones.index') }}" class="btn btn-outline-secondary w-100">Limpiar Filtros</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opiniones as $opinion)
                        <tr>
                            <td class="text-nowrap">{{ $opinion->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div>{{ $opinion->user->nombreCompleto }}</div>
                                <small class="text-muted">{{ $opinion->user->email }}</small>
                            </td>
                            <td>
                                <a href="{{ route('producto.detalle', $opinion->producto_id) }}" target="_blank" class="text-decoration-none text-cafe fw-bold">
                                    {{ $opinion->producto->nombre }}
                                </a>
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi bi-star{{ $i <= $opinion->calificacion ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <p class="mb-1 small">{{ Str::limit($opinion->comentario, 100) }}</p>
                                @if($opinion->imagenes->isNotEmpty())
                                    <span class="badge bg-secondary"><i class="bi bi-image"></i> {{ $opinion->imagenes->count() }} fotos</span>
                                @endif
                                @if($opinion->respuesta_admin)
                                    <span class="badge bg-cafe-light text-cafe"><i class="bi bi-reply"></i> Respondido</span>
                                @endif
                            </td>
                            <td>
                                @if($opinion->estado === 'visible')
                                    <span class="badge bg-success">Visible</span>
                                @elseif($opinion->estado === 'oculto')
                                    <span class="badge bg-secondary">Oculto</span>
                                @else
                                    <span class="badge bg-danger">Reportado</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalOpinion{{ $opinion->id }}" title="Ver / Responder">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <form action="{{ route('admin.opiniones.cambiarEstado', $opinion->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    @if($opinion->estado === 'visible')
                                        <input type="hidden" name="estado" value="oculto">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Ocultar" onclick="return confirm('¿Seguro que deseas ocultar esta reseña?')">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    @else
                                        <input type="hidden" name="estado" value="visible">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Hacer Visible" onclick="return confirm('¿Seguro que deseas hacer visible esta reseña?')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endif
                                </form>
                                
                                <form action="{{ route('admin.opiniones.destroy', $opinion->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar esta reseña permanentemente?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Detalles y Responder -->
                        <div class="modal fade" id="modalOpinion{{ $opinion->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Detalle de Reseña</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between">
                                                <h6><strong>Producto:</strong> {{ $opinion->producto->nombre }}</h6>
                                                <span class="text-muted">{{ $opinion->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="text-warning mb-2 fs-5">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="bi bi-star{{ $i <= $opinion->calificacion ? '-fill' : '' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="mb-2">{{ $opinion->comentario }}</p>
                                            
                                            @if($opinion->etiquetas)
                                                <div class="mb-3">
                                                    @foreach($opinion->etiquetas as $etiqueta)
                                                        <span class="badge bg-light text-dark border">{{ $etiqueta }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($opinion->imagenes->isNotEmpty())
                                                <div class="d-flex gap-2 mb-3">
                                                    @foreach($opinion->imagenes as $img)
                                                        <a href="{{ Storage::url($img->imagen) }}" target="_blank">
                                                            <img src="{{ Storage::url($img->imagen) }}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <form action="{{ route('admin.opiniones.responder', $opinion->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-cafe">Respuesta de Café Aurora</label>
                                                <textarea class="form-control" name="respuesta_admin" rows="4" placeholder="Escribe una respuesta para el cliente..." required>{{ $opinion->respuesta_admin }}</textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-cafe"><i class="bi bi-reply"></i> Guardar Respuesta</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-chat-left-dots fs-1 mb-2 d-block"></i>
                                No se encontraron opiniones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $opiniones->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection