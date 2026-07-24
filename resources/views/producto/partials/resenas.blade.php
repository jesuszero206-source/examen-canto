<div class="mt-5 pt-4 border-top" id="seccion-resenas">
    <h3 class="fw-bold mb-4">Opiniones de Clientes</h3>

    <div class="row g-4">
        <!-- Resumen de calificaciones -->
        <div class="col-md-4">
            <div class="card bg-light border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <h1 class="display-4 fw-bold text-cafe mb-0">{{ $producto->promedio_calificacion }}</h1>
                    <div class="text-warning fs-4 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($producto->promedio_calificacion))
                                <i class="bi bi-star-fill"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-muted mb-4">{{ $producto->total_calificaciones }} opiniones</p>

                    <!-- Barras de distribución -->
                    <div class="text-start">
                        @foreach($distribucion as $estrellas => $data)
                            <div class="d-flex align-items-center mb-2 small">
                                <span class="me-2 text-muted" style="width: 40px;">{{ $estrellas }} <i class="bi bi-star-fill text-warning"></i></span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $data['porcentaje'] }}%" aria-valuenow="{{ $data['porcentaje'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ms-2 text-muted" style="width: 30px; text-align: right;">{{ $data['porcentaje'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Botón para calificar (si puede) -->
            @auth
                @if($miCalificacion)
                    <div class="mt-4 p-4 border rounded-4">
                        <h6 class="fw-bold"><i class="bi bi-check-circle-fill text-success me-2"></i> Ya calificaste este producto</h6>
                        <div class="text-warning mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="bi bi-star{{ $i <= $miCalificacion->calificacion ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                        <p class="small text-muted mb-3">{{ $miCalificacion->comentario }}</p>
                        <button class="btn btn-outline-danger btn-sm w-100" onclick="eliminarResena({{ $miCalificacion->id }})">Eliminar mi reseña</button>
                    </div>
                @elseif($puedeCalificar)
                    <button class="btn btn-cafe w-100 mt-4 py-2" data-bs-toggle="modal" data-bs-target="#modalCalificar">
                        Escribir una opinión
                    </button>
                @else
                    <div class="alert alert-secondary mt-4 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> Para calificar este producto debes haberlo comprado previamente y el pedido debe estar completado.
                    </div>
                @endif
            @else
                <div class="alert alert-secondary mt-4 mb-0 small text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-cafe">Inicia sesión</a> para dejar tu opinión.
                </div>
            @endauth
        </div>

        <!-- Lista de reseñas -->
        <div class="col-md-8">
            @if($calificaciones->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-left-dots fs-1 mb-3 d-block"></i>
                    <p>Aún no hay opiniones para este producto.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-4">
                    @foreach($calificaciones as $calificacion)
                        <div class="card border-0 border-bottom rounded-0 pb-3">
                            <div class="card-body p-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-cafe fw-bold" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($calificacion->user->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $calificacion->user->nombreCompleto }}</h6>
                                            <small class="text-success"><i class="bi bi-check-circle-fill"></i> Compra verificada</small>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $calificacion->created_at->diffForHumans() }}</small>
                                </div>
                                
                                <div class="text-warning mb-2 small">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi bi-star{{ $i <= $calificacion->calificacion ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                
                                <p class="mb-3">{{ $calificacion->comentario }}</p>

                                @if($calificacion->etiquetas)
                                    <div class="mb-3">
                                        @foreach($calificacion->etiquetas as $etiqueta)
                                            <span class="badge bg-light text-dark border fw-normal">{{ $etiqueta }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($calificacion->imagenes->isNotEmpty())
                                    <div class="d-flex gap-2 mb-3">
                                        @foreach($calificacion->imagenes as $img)
                                            <a href="{{ Storage::url($img->imagen) }}" target="_blank">
                                                <img src="{{ Storage::url($img->imagen) }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;" alt="Imagen reseña">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if($calificacion->respuesta_admin)
                                    <div class="bg-light p-3 rounded-3 mb-3 ms-4 border-start border-4 border-cafe">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-shop text-cafe"></i>
                                            <span class="fw-bold small text-cafe">Respuesta de Café Aurora</span>
                                        </div>
                                        <p class="mb-0 small text-muted">{{ $calificacion->respuesta_admin }}</p>
                                    </div>
                                @endif

                                <!-- Votos -->
                                @auth
                                    @if(Auth::id() !== $calificacion->user_id)
                                        <div class="d-flex gap-3 align-items-center">
                                            <small class="text-muted">¿Te resultó útil?</small>
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 btn-votar" data-id="{{ $calificacion->id }}" data-voto="1">
                                                <i class="bi bi-hand-thumbs-up"></i> Sí
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 btn-votar" data-id="{{ $calificacion->id }}" data-voto="-1">
                                                <i class="bi bi-hand-thumbs-down"></i> No
                                            </button>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $calificaciones->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Calificar -->
@auth
@if($puedeCalificar && !$miCalificacion)
<div class="modal fade" id="modalCalificar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Calificar {{ $producto->nombre }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCalificar" onsubmit="enviarResena(event)">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="estrellas-input fs-1 text-muted" id="estrellasContainer">
                            <i class="bi bi-star cursor-pointer" data-valor="1"></i>
                            <i class="bi bi-star cursor-pointer" data-valor="2"></i>
                            <i class="bi bi-star cursor-pointer" data-valor="3"></i>
                            <i class="bi bi-star cursor-pointer" data-valor="4"></i>
                            <i class="bi bi-star cursor-pointer" data-valor="5"></i>
                        </div>
                        <input type="hidden" name="calificacion" id="calificacionValue" required>
                        <div class="small text-danger d-none" id="errorEstrellas">Por favor selecciona una calificación</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comentario</label>
                        <textarea class="form-control" name="comentario" rows="4" placeholder="¿Qué te pareció el producto? (Opcional)" maxlength="1000"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Etiquetas</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $opcionesEtiquetas = ['Excelente sabor', 'Muy buena presentación', 'Llegó caliente', 'Buen precio', 'Lo volvería a comprar'];
                            @endphp
                            @foreach($opcionesEtiquetas as $etiqueta)
                                <input type="checkbox" class="btn-check" name="etiquetas[]" id="etiqueta_{{ $loop->index }}" value="{{ $etiqueta }}">
                                <label class="btn btn-outline-secondary rounded-pill btn-sm" for="etiqueta_{{ $loop->index }}">{{ $etiqueta }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fotos (Max. 3)</label>
                        <input class="form-control" type="file" name="imagenes[]" accept="image/jpeg,image/png,image/webp" multiple max="3">
                        <small class="text-muted">Opcional. Formatos soportados: JPG, PNG, WEBP.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-cafe rounded-pill px-4" id="btnSubmitResena">
                        <i class="bi bi-send me-2"></i> Enviar Reseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; transition: color 0.2s ease; }
.estrellas-input .bi-star-fill { color: #ffc107 !important; }
</style>

<script>
    // Estrellas dinámicas
    document.addEventListener('DOMContentLoaded', function() {
        const estrellas = document.querySelectorAll('#estrellasContainer i');
        const calificacionInput = document.getElementById('calificacionValue');
        let calificacionActual = 0;

        estrellas.forEach(estrella => {
            estrella.addEventListener('mouseover', function() {
                const valor = this.getAttribute('data-valor');
                actualizarEstrellasUI(valor);
            });

            estrella.addEventListener('mouseout', function() {
                actualizarEstrellasUI(calificacionActual);
            });

            estrella.addEventListener('click', function() {
                calificacionActual = this.getAttribute('data-valor');
                calificacionInput.value = calificacionActual;
                document.getElementById('errorEstrellas').classList.add('d-none');
            });
        });

        function actualizarEstrellasUI(valor) {
            estrellas.forEach(e => {
                const eValor = e.getAttribute('data-valor');
                if (eValor <= valor) {
                    e.classList.remove('bi-star');
                    e.classList.add('bi-star-fill', 'text-warning');
                } else {
                    e.classList.add('bi-star');
                    e.classList.remove('bi-star-fill', 'text-warning');
                }
            });
        }
    });

    async function enviarResena(e) {
        e.preventDefault();
        const form = document.getElementById('formCalificar');
        const formData = new FormData(form);
        const btnSubmit = document.getElementById('btnSubmitResena');
        
        if (!formData.get('calificacion')) {
            document.getElementById('errorEstrellas').classList.remove('d-none');
            return;
        }

        const archivos = form.querySelector('input[type="file"]').files;
        if (archivos.length > 3) {
            alert('Solo puedes subir un máximo de 3 imágenes.');
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';

        try {
            const response = await fetch('{{ route('calificaciones.store', $producto->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Recargar para ver los cambios rápidamente
                window.location.reload();
            } else {
                alert(data.message || 'Ocurrió un error al enviar la reseña.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="bi bi-send me-2"></i> Enviar Reseña';
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-send me-2"></i> Enviar Reseña';
        }
    }
</script>
@endif

<script>
    function eliminarResena(id) {
        if(confirm('¿Estás seguro de eliminar tu reseña?')) {
            fetch(`/calificaciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if(data.success) window.location.reload();
              });
        }
    }

    document.querySelectorAll('.btn-votar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const voto = this.getAttribute('data-voto');
            
            fetch(`/calificaciones/${id}/votar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ voto: voto })
            }).then(response => response.json())
              .then(data => {
                  if(data.success) {
                      alert('Gracias por tu feedback.');
                      this.classList.add('active', 'btn-secondary', 'text-white');
                      this.classList.remove('btn-outline-secondary');
                  }
              });
        });
    });
</script>
@endauth