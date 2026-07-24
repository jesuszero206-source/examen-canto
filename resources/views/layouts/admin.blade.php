<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | Café Aurora</title>
    
    @vite(['resources/css/admin.scss', 'resources/js/app.js', 'resources/js/admin.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="admin-body bg-light">
    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-cup-hot-fill text-cafe-light"></i>
                Café Aurora Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3 d-none d-lg-block">
                        <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Ver Tienda
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                                {{ substr(Auth::user()->nombre, 0, 1) }}
                            </div>
                            <span>{{ Auth::user()->nombre }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('perfil.show') }}"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-sm" style="min-height: calc(100vh - 56px);">
                <div class="position-sticky pt-4 px-2">
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.dashboard') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
                            <span>Gestión</span>
                        </h6>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.pedidos.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.pedidos.index') }}">
                                <i class="bi bi-receipt me-2"></i> Pedidos
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.productos.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.productos.index') }}">
                                <i class="bi bi-box-seam me-2"></i> Productos
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.categorias.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.categorias.index') }}">
                                <i class="bi bi-tags me-2"></i> Categorías
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.inventario.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.inventario.index') }}">
                                <i class="bi bi-boxes me-2"></i> Inventario
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.usuarios.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.usuarios.index') }}">
                                <i class="bi bi-people me-2"></i> Usuarios
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.opiniones.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.opiniones.index') }}">
                                <i class="bi bi-chat-left-dots me-2"></i> Opiniones
                            </a>
                        </li>
                        
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
                            <span>Restaurante</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.mesas.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.mesas.index') }}">
                                <i class="bi bi-display me-2"></i> Mesas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.reservas.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.reservas.index') }}">
                                <i class="bi bi-calendar-check me-2"></i> Reservas
                            </a>
                        </li>
                        
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
                            <span>Sistema</span>
                        </h6>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.reportes.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.reportes.index') }}">
                                <i class="bi bi-bar-chart me-2"></i> Reportes
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link rounded-3 {{ request()->routeIs('admin.auditoria.*') ? 'active bg-cafe text-white' : 'text-dark' }}" href="{{ route('admin.auditoria.index') }}">
                                <i class="bi bi-shield-check me-2"></i> Auditoría
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2 fw-bold text-dark">@yield('title')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('actions')
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-success text-white" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-danger text-white" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 bg-danger text-white">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
