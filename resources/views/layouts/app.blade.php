<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Café Aurora') | Sistema Web</title>
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-cafe sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-cup-hot-fill"></i>
                Café Aurora
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reservas.index') ? 'active' : '' }}" href="{{ route('reservas.index') }}">Reservar Mesa</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('carrito.index') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span id="cart-badge" class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger {{ (!isset($carritoItemCount) || $carritoItemCount == 0) ? 'd-none' : '' }}">
                                {{ $carritoItemCount ?? 0 }}
                            </span>
                        </a>
                    </li>
                    
                    @guest
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                    @else
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-cafe text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                                    {{ substr(Auth::user()->nombre, 0, 1) }}
                                </div>
                                {{ Auth::user()->nombre }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('perfil.show') }}"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="{{ route('perfil.reservas.index') }}"><i class="bi bi-journal-bookmark me-2"></i> Mis Reservas</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @auth
    @php
        $unreadReservas = \App\Models\Reserva::where('user_id', Auth::id())
            ->where('notificado_cliente', false)
            ->whereIn('estado', ['confirmada', 'rechazada', 'cancelada'])
            ->get();
    @endphp
    @if($unreadReservas->count() > 0)
        <div class="container mt-4 mb-0 animate__animated animate__fadeInDown">
            @foreach($unreadReservas as $unread)
                <div class="alert alert-{{ $unread->estado == 'confirmada' ? 'success' : ($unread->estado == 'rechazada' ? 'danger' : 'warning') }} alert-dismissible fade show shadow-sm" role="alert">
                    @if($unread->estado == 'confirmada')
                        <h5 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i> Reserva Confirmada</h5>
                        <p class="mb-0">Tu reserva para el día <strong>{{ $unread->fecha->format('d/m/Y') }}</strong> a las <strong>{{ $unread->hora->format('h:i A') }}</strong> ha sido confirmada.</p>
                        <hr>
                        <p class="mb-0">Mesa asignada: <strong>#{{ $unread->mesa ? $unread->mesa->numero : 'Pendiente' }}</strong> | Ubicación: <strong>{{ $unread->mesa ? ucfirst($unread->mesa->ubicacion) : ucfirst($unread->ubicacion_preferida) }}</strong></p>
                        <p class="mb-0 mt-2 text-dark">Te esperamos en Café Aurora.</p>
                    @elseif($unread->estado == 'rechazada')
                        <h5 class="alert-heading"><i class="bi bi-x-circle-fill me-2"></i> Reserva Rechazada</h5>
                        <p class="mb-0">Lo sentimos, tu reserva para el día <strong>{{ $unread->fecha->format('d/m/Y') }}</strong> no pudo ser confirmada.</p>
                        <hr>
                        <p class="mb-0">Motivo: <strong>{{ $unread->motivo_estado ?? 'Sin especificar' }}</strong></p>
                    @elseif($unread->estado == 'cancelada')
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> Reserva Cancelada</h5>
                        <p class="mb-0">La reserva para el día <strong>{{ $unread->fecha->format('d/m/Y') }}</strong> fue cancelada.</p>
                    @endif
                    <button type="button" class="btn-close btn-mark-read" data-id="{{ $unread->id }}" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.btn-mark-read').forEach(btn => {
                    btn.addEventListener('click', function() {
                        fetch('{{ route('perfil.reservas.notificacion.leida') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                    });
                });
            });
        </script>
    @endif
    @endauth

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-cafe">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-cup-hot-fill text-success"></i> Café Aurora
                    </h5>
                    <p>Sirviendo el mejor café de la ciudad desde 2024. Nuestro compromiso es la calidad y el mejor ambiente.</p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{ route('home') }}">Catálogo</a></li>
                        <li><a href="{{ route('carrito.index') }}">Carrito</a></li>
                        <li><a href="{{ route('login') }}">Mi Cuenta</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Horarios</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li>Lunes - Viernes: 7am - 9pm</li>
                        <li>Sábados: 8am - 10pm</li>
                        <li>Domingos: 8am - 8pm</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><i class="bi bi-geo-alt me-2"></i> Av. Principal 123, Centro</li>
                        <li><i class="bi bi-telephone me-2"></i> (555) 123-4567</li>
                        <li><i class="bi bi-envelope me-2"></i> hola@cafeaurora.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Café Aurora. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Toasts -->
    <x-toast />

    <!-- AJAX Add to Cart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.form-add-to-cart');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const btn = this.querySelector('button[type="submit"]');
                    const textSpan = btn.querySelector('.btn-text');
                    const originalText = textSpan.innerText;
                    
                    textSpan.innerText = 'Agregando...';
                    btn.disabled = true;

                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.status === 401 || response.redirected) {
                            window.location.href = "{{ route('login') }}";
                            return null;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data) return;
                        
                        btn.disabled = false;
                        textSpan.innerText = originalText;
                        
                        if (data.success) {
                            const badge = document.getElementById('cart-badge');
                            if (badge) {
                                badge.innerText = data.carritoItemCount;
                                badge.classList.remove('d-none');
                                // Animation effect
                                badge.classList.add('animate__animated', 'animate__bounceIn');
                                setTimeout(() => badge.classList.remove('animate__animated', 'animate__bounceIn'), 1000);
                            }
                            
                            // Visual feedback on button
                            const originalBg = btn.style.backgroundColor;
                            const originalColor = btn.style.color;
                            
                            btn.style.backgroundColor = '#198754'; // success
                            btn.style.color = '#fff';
                            textSpan.innerText = '¡Agregado!';
                            
                            setTimeout(() => {
                                btn.style.backgroundColor = originalBg;
                                btn.style.color = originalColor;
                                textSpan.innerText = originalText;
                            }, 2000);
                        } else {
                            alert(data.message || 'Error al agregar al carrito');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.disabled = false;
                        textSpan.innerText = originalText;
                    });
                });
            });
        });
    </script>
</body>
</html>
