@extends('layouts.admin')

@section('title', 'Calendario y Plano de Reservas')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .fc-event { cursor: pointer; border: none; border-radius: 4px; padding: 2px; color: white !important;}
    .fc-header-toolbar button { text-transform: capitalize; border-radius: 50rem !important; }
    .mesa-plano { 
        transition: all 0.3s ease; 
        cursor: pointer;
        border-width: 3px !important;
    }
    .mesa-plano.seleccionada {
        border-color: #0d6efd !important;
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
        transform: scale(1.05);
    }
    .mesa-plano.highlight {
        animation: pulse 1.5s infinite;
        border-color: #ffc107 !important;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    .nav-tabs .nav-link { color: #555; font-weight: 500; }
    .nav-tabs .nav-link.active { font-weight: bold; color: #4e342e; border-bottom: 3px solid #4e342e; }
    .toast-container { z-index: 1060; }
</style>
@endpush

@section('content')
<!-- Contenedor Toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage">Acción exitosa</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Centro de Operaciones de Reservas</h4>
    <div>
        <button class="btn btn-cafe rounded-pill px-4" onclick="abrirModalNueva()">
            <i class="bi bi-plus-lg me-2"></i> Nueva Reserva
        </button>
    </div>
</div>

<!-- Métricas -->
<div class="row g-3 mb-4 text-center">
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0"><div class="card-body p-2"><h6 class="text-muted mb-1">Total</h6><h4 class="fw-bold mb-0" id="metrica-total">0</h4></div></div></div>
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0 border-bottom border-warning border-4"><div class="card-body p-2"><h6 class="text-muted mb-1">Pendientes</h6><h4 class="fw-bold mb-0" id="metrica-pendientes">0</h4></div></div></div>
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0 border-bottom border-success border-4"><div class="card-body p-2"><h6 class="text-muted mb-1">Confirmadas</h6><h4 class="fw-bold mb-0" id="metrica-confirmadas">0</h4></div></div></div>
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0 border-bottom border-danger border-4"><div class="card-body p-2"><h6 class="text-muted mb-1">Rechazadas</h6><h4 class="fw-bold mb-0" id="metrica-rechazadas">0</h4></div></div></div>
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0 border-bottom border-info border-4"><div class="card-body p-2"><h6 class="text-muted mb-1">Finalizadas</h6><h4 class="fw-bold mb-0" id="metrica-finalizadas">0</h4></div></div></div>
    <div class="col-6 col-md-3 col-xl-2"><div class="card shadow-sm border-0 border-bottom border-secondary border-4"><div class="card-body p-2"><h6 class="text-muted mb-1">Canceladas</h6><h4 class="fw-bold mb-0" id="metrica-canceladas">0</h4></div></div></div>
</div>

<div class="row g-4">
    <!-- Calendario -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-center gap-3 mb-3 small">
                    <span class="badge bg-warning text-dark"><i class="bi bi-circle-fill"></i> Pendiente</span>
                    <span class="badge bg-success"><i class="bi bi-circle-fill"></i> Confirmada</span>
                    <span class="badge bg-danger"><i class="bi bi-circle-fill"></i> Rechazada</span>
                    <span class="badge bg-info text-dark"><i class="bi bi-circle-fill"></i> Finalizada</span>
                    <span class="badge bg-secondary"><i class="bi bi-circle-fill"></i> Cancelada</span>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    
    <!-- Plano de Mesas -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-3">
                <h5 class="fw-bold text-center mb-3">Plano de Mesas</h5>
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-4 small text-muted">
                    <span><i class="bi bi-square-fill text-success"></i> Disponible</span>
                    <span><i class="bi bi-square-fill text-warning"></i> Reservada</span>
                    <span><i class="bi bi-square-fill text-danger"></i> Ocupada</span>
                    <span><i class="bi bi-square-fill text-primary"></i> Sel.</span>
                    <span><i class="bi bi-square-fill text-secondary"></i> Inactiva</span>
                </div>

                @php $ubicaciones = $mesas->groupBy('ubicacion'); @endphp
                @foreach($ubicaciones as $ubicacion => $mesasGrupo)
                    <h6 class="fw-bold text-uppercase text-muted border-bottom pb-1">{{ $ubicacion }}</h6>
                    <div class="row g-2 mb-3">
                        @foreach($mesasGrupo as $mesa)
                            <div class="col-4">
                                <div class="card text-center p-2 mesa-plano border-{{ $mesa->estado == 'disponible' ? 'success' : ($mesa->estado == 'reservada' ? 'warning' : ($mesa->estado == 'ocupada' ? 'danger' : 'secondary')) }} bg-white" 
                                     id="plano-mesa-{{ $mesa->id }}" 
                                     data-id="{{ $mesa->id }}"
                                     data-estado="{{ $mesa->estado }}"
                                     onclick="seleccionarMesaPlano({{ $mesa->id }}, '{{ $mesa->estado }}')">
                                    <i class="bi bi-display" style="font-size: 1.5rem;"></i>
                                    <div class="fw-bold small mt-1">M-{{ $mesa->numero }}</div>
                                    <div style="font-size: 0.7rem;"><i class="bi bi-people"></i> {{ $mesa->capacidad }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Gestión Reserva -->
<div class="modal fade" id="modalReserva" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalReservaTitle">Gestionar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs px-3 pt-3 border-0" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-info">Información</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cliente">Cliente</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-mesa">Asignar Mesa</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-historial">Historial</a></li>
                </ul>
                
                <form id="formReserva" class="p-4 bg-light">
                    @csrf
                    <input type="hidden" id="reserva_id" name="id">
                    
                    <div class="tab-content">
                        <!-- INFO -->
                        <div class="tab-pane fade show active" id="tab-info">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="reserva_fecha" name="fecha" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Hora</label>
                                    <input type="time" class="form-control" id="reserva_hora" name="hora" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Personas</label>
                                    <input type="number" class="form-control" id="reserva_personas" name="personas" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estado Actual</label>
                                    <input type="text" class="form-control" id="reserva_estado" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observaciones</label>
                                    <textarea class="form-control" id="reserva_observaciones" name="observaciones" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CLIENTE -->
                        <div class="tab-pane fade" id="tab-cliente">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" class="form-control" id="reserva_nombre" name="nombre_reserva" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" id="reserva_telefono" name="telefono" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Correo</label>
                                    <input type="email" class="form-control" id="reserva_correo" name="correo">
                                </div>
                            </div>
                        </div>
                        
                        <!-- MESA -->
                        <div class="tab-pane fade" id="tab-mesa">
                            <div class="alert alert-info border-0 rounded-3">
                                <i class="bi bi-info-circle me-2"></i> Puedes seleccionar la mesa directamente desde el plano lateral.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mesa Asignada</label>
                                <select class="form-select" id="reserva_mesa_id" name="mesa_id" onchange="seleccionarMesaDesdeSelect(this.value)">
                                    <option value="">Sin Asignar</option>
                                    @foreach($mesas as $mesa)
                                        <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} ({{ $mesa->capacidad }} pers. - {{ ucfirst($mesa->ubicacion) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- HISTORIAL -->
                        <div class="tab-pane fade" id="tab-historial">
                            <div class="list-group list-group-flush border rounded-3 bg-white" id="historialLista" style="max-height: 250px; overflow-y:auto;">
                                <!-- Llenado dinámico por JS -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Botones de Acción -->
            <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <button type="button" class="btn btn-outline-danger" onclick="eliminarReserva()"><i class="bi bi-trash"></i> Eliminar</button>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-warning" onclick="cambiarEstado('cancelada')"><i class="bi bi-x-circle"></i> Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="cambiarEstado('rechazada')"><i class="bi bi-slash-circle"></i> Rechazar</button>
                    <button type="button" class="btn btn-info text-white" onclick="cambiarEstado('finalizada')"><i class="bi bi-check2-all"></i> Finalizar</button>
                    <button type="button" class="btn btn-success" onclick="cambiarEstado('confirmada')"><i class="bi bi-check-circle"></i> Confirmar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicion()"><i class="bi bi-save"></i> Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- FullCalendar JS Core y Plugins (v6) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.11/index.global.min.js"></script>

<script>
    let calendar;
    let modalActivo = false;
    let csrfToken = '{{ csrf_token() }}';

    document.addEventListener('DOMContentLoaded', function() {
        actualizarMetricas();
        
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Agenda'
            },
            height: 'auto',
            events: '{{ route('admin.reservas.eventos') }}',
            editable: true,
            droppable: true,
            eventDrop: function(info) {
                if(!confirm('¿Seguro que deseas mover la reserva a esta nueva fecha/hora?')) {
                    info.revert();
                    return;
                }
                moverReservaAjax(info);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                abrirModalReserva(info.event.id);
            },
            eventContent: function(arg) {
                let horaFormateada = arg.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                let mesaText = arg.event.extendedProps.mesa_numero;
                let html = `
                    <div class="p-1 px-2 w-100 overflow-hidden" title="${arg.event.title} - Mesa ${mesaText}">
                        <div class="fw-bold"><i class="bi bi-clock"></i> ${horaFormateada}</div>
                        <div class="text-truncate">${arg.event.title}</div>
                        <div class="small"><i class="bi bi-display"></i> Mesa ${mesaText}</div>
                    </div>
                `;
                return { html: html };
            }
        });
        
        calendar.render();

        var modalEl = document.getElementById('modalReserva');
        modalEl.addEventListener('show.bs.modal', () => modalActivo = true);
        modalEl.addEventListener('hide.bs.modal', () => {
            modalActivo = false;
            limpiarPlano();
        });
    });

    function mostrarToast(mensaje, tipo = 'success') {
        const toastEl = document.getElementById('liveToast');
        const msgEl = document.getElementById('toastMessage');
        msgEl.innerText = mensaje;
        toastEl.className = `toast align-items-center text-bg-${tipo} border-0`;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    function actualizarMetricas() {
        fetch('{{ route('admin.reservas.metricas') }}')
        .then(res => res.json())
        .then(data => {
            document.getElementById('metrica-total').innerText = data.total;
            document.getElementById('metrica-pendientes').innerText = data.pendientes;
            document.getElementById('metrica-confirmadas').innerText = data.confirmadas;
            document.getElementById('metrica-rechazadas').innerText = data.rechazadas;
            document.getElementById('metrica-canceladas').innerText = data.canceladas;
            document.getElementById('metrica-finalizadas').innerText = data.finalizadas;
        });
    }

    function seleccionarMesaPlano(mesaId, estado) {
        if(!modalActivo) return; 
        if(estado === 'fuera_de_servicio') {
            mostrarToast('Mesa inhabilitada', 'warning');
            return;
        }
        limpiarPlano();
        document.getElementById('plano-mesa-'+mesaId).classList.add('seleccionada');
        const select = document.getElementById('reserva_mesa_id');
        if(select) select.value = mesaId;
        new bootstrap.Tab(document.querySelector('a[href="#tab-mesa"]')).show();
    }

    function seleccionarMesaDesdeSelect(mesaId) {
        limpiarPlano();
        if(mesaId) {
            let el = document.getElementById('plano-mesa-'+mesaId);
            if(el) el.classList.add('seleccionada');
        }
    }

    function limpiarPlano() {
        document.querySelectorAll('.mesa-plano').forEach(el => {
            el.classList.remove('seleccionada', 'highlight');
        });
    }

    function resaltarMesaEnPlano(mesaId) {
        limpiarPlano();
        if(mesaId) {
            let el = document.getElementById('plano-mesa-'+mesaId);
            if(el) el.classList.add('highlight');
        }
    }

    function abrirModalNueva() {
        document.getElementById('formReserva').reset();
        document.getElementById('reserva_id').value = '';
        document.getElementById('reserva_estado').value = 'NUEVA';
        document.getElementById('historialLista').innerHTML = '<div class="p-3 text-center text-muted">Disponible una vez guardada la reserva.</div>';
        limpiarPlano();
        new bootstrap.Tab(document.querySelector('a[href="#tab-info"]')).show();
        new bootstrap.Modal(document.getElementById('modalReserva')).show();
    }

    function abrirModalReserva(id) {
        fetch(`{{ url('admin/reservas') }}/${id}/info`)
        .then(res => res.json())
        .then(data => {
            let r = data.reserva;
            document.getElementById('reserva_id').value = r.id;
            document.getElementById('reserva_fecha').value = r.fecha.substring(0,10);
            document.getElementById('reserva_hora').value = r.hora.substring(11,16);
            document.getElementById('reserva_personas').value = r.personas;
            document.getElementById('reserva_estado').value = r.estado.toUpperCase();
            document.getElementById('reserva_observaciones').value = r.observaciones || '';
            document.getElementById('reserva_nombre').value = r.nombre_reserva;
            document.getElementById('reserva_telefono').value = r.telefono;
            document.getElementById('reserva_correo').value = r.correo || '';
            document.getElementById('reserva_mesa_id').value = r.mesa_id || '';
            
            let htmlHist = '';
            data.historial.forEach(log => {
                let fecha = new Date(log.created_at).toLocaleString('es-MX');
                let usuario = log.user ? log.user.name : 'Sistema';
                htmlHist += `<div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                  <h6 class="mb-1 fw-bold">${log.action}</h6>
                                  <small class="text-muted">${fecha}</small>
                                </div>
                                <small class="text-muted">Por: ${usuario}</small>
                             </div>`;
            });
            document.getElementById('historialLista').innerHTML = htmlHist || '<div class="p-3 text-center text-muted">Sin historial</div>';
            
            resaltarMesaEnPlano(r.mesa_id);
            new bootstrap.Tab(document.querySelector('a[href="#tab-info"]')).show();
            new bootstrap.Modal(document.getElementById('modalReserva')).show();
        });
    }

    function moverReservaAjax(info) {
        // En FC v6, start puede ajustarse por timezone, extraemos fecha real enviando strings locales
        let fecha = info.event.startStr.substring(0,10);
        let horaStr = info.event.startStr.substring(11,19);
        
        // Si startStr no tiene hora (ej. vista mensual arrastra), calculamos localmente
        if(!horaStr) {
            let offset = info.event.start.getTimezoneOffset() * 60000;
            let localISOTime = (new Date(info.event.start - offset)).toISOString().slice(0, -1);
            fecha = localISOTime.substring(0,10);
            horaStr = localISOTime.substring(11,19);
        }

        fetch(`{{ url('admin/reservas') }}/${info.event.id}/drag`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ fecha: fecha, hora: horaStr })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                mostrarToast(data.message);
                actualizarMetricas();
            } else {
                mostrarToast(data.message || 'Error', 'danger');
                info.revert();
            }
        }).catch(err => {
            mostrarToast('Error de conexión', 'danger');
            info.revert();
        });
    }

    function cambiarEstado(estadoNuevo) {
        let id = document.getElementById('reserva_id').value;
        if(!id) {
            mostrarToast('Guarda la reserva primero.', 'warning');
            return;
        }

        let mesa_id = document.getElementById('reserva_mesa_id').value;
        let data = { estado: estadoNuevo, mesa_id: mesa_id };
        
        if (estadoNuevo === 'rechazada') {
            let motivo = prompt("Motivo del rechazo:");
            if (!motivo) return;
            data.motivo_estado = motivo;
        }

        fetch(`{{ url('admin/reservas') }}/${id}/estado`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                mostrarToast(data.message);
                calendar.refetchEvents();
                actualizarMetricas();
                document.getElementById('reserva_estado').value = estadoNuevo.toUpperCase();
            } else {
                mostrarToast(data.message, 'danger');
            }
        });
    }

    function guardarEdicion() {
        let id = document.getElementById('reserva_id').value;
        let url = id ? `{{ url('admin/reservas') }}/${id}` : `{{ route('admin.reservas.store') }}`;
        let method = id ? 'PUT' : 'POST';
        
        let formData = new FormData(document.getElementById('formReserva'));
        if(!formData.get('estado') || document.getElementById('reserva_estado').value === 'NUEVA') {
            formData.append('estado', 'pendiente');
        }
        
        let object = {};
        formData.forEach((value, key) => object[key] = value);

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(object)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                mostrarToast(data.message);
                calendar.refetchEvents();
                actualizarMetricas();
                if(!id) bootstrap.Modal.getInstance(document.getElementById('modalReserva')).hide();
            } else {
                mostrarToast(data.message || 'Revisa los datos.', 'danger');
            }
        }).catch(() => {
            mostrarToast('Conflicto de horario o faltan datos obligatorios.', 'danger');
        });
    }

    function eliminarReserva() {
        let id = document.getElementById('reserva_id').value;
        if(!id) return;

        if(!confirm('¿Eliminar permanentemente?')) return;

        fetch(`{{ url('admin/reservas') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                mostrarToast(data.message);
                calendar.refetchEvents();
                actualizarMetricas();
                bootstrap.Modal.getInstance(document.getElementById('modalReserva')).hide();
            } else {
                mostrarToast(data.message, 'danger');
            }
        });
    }
</script>
@endpush
