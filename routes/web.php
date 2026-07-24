<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductoController as AdminProductoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\AuditoriaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
    // Home y Catálogo
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Reservas Cliente
    Route::get('/reservas', [\App\Http\Controllers\ReservaClienteController::class, 'index'])->name('reservas.index');
    Route::post('/reservas', [\App\Http\Controllers\ReservaClienteController::class, 'store'])->name('reservas.store');
Route::get('/producto/{id}', [ProductoController::class, 'show'])->name('producto.detalle');

/*
|--------------------------------------------------------------------------
| Autenticación (solo guests)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Carrito (requiere autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/aumentar/{id}', [CarritoController::class, 'aumentar'])->name('carrito.aumentar');
    Route::post('/carrito/disminuir/{id}', [CarritoController::class, 'disminuir'])->name('carrito.disminuir');
    Route::post('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
});

/*
|--------------------------------------------------------------------------
| Checkout (autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmacion/{pedido}', [CheckoutController::class, 'confirmacion'])->name('checkout.confirmacion');
});

/*
|--------------------------------------------------------------------------
| Perfil (autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('perfil')->name('perfil.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
});

/*
|--------------------------------------------------------------------------
| Mis Reservas (autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('perfil/reservas')->name('perfil.reservas.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReservaClienteController::class, 'misReservas'])->name('index');
    Route::put('/{reserva}/cancelar', [\App\Http\Controllers\ReservaClienteController::class, 'cancelar'])->name('cancelar');
    Route::post('/notificacion-leida', [\App\Http\Controllers\ReservaClienteController::class, 'marcarNotificacionLeida'])->name('notificacion.leida');
});

/*
|--------------------------------------------------------------------------
| Calificaciones (autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/producto/{productoId}/calificar', [\App\Http\Controllers\CalificacionController::class, 'store'])->name('calificaciones.store');
    Route::delete('/calificaciones/{calificacion}', [\App\Http\Controllers\CalificacionController::class, 'destroy'])->name('calificaciones.destroy');
    Route::post('/calificaciones/{calificacion}/votar', [\App\Http\Controllers\CalificacionController::class, 'votar'])->name('calificaciones.votar');
});

/*
|--------------------------------------------------------------------------
| Admin (autenticado + rol Administrador)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Productos CRUD
    Route::get('/productos', [AdminProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [AdminProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/editar', [AdminProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [AdminProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [AdminProductoController::class, 'destroy'])->name('productos.destroy');

    // Categorías CRUD
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');

    // Usuarios CRUD
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{producto}/ajustar', [InventarioController::class, 'ajustar'])->name('inventario.ajustar');
    Route::put('/inventario/movimiento/{movimiento}', [InventarioController::class, 'updateMovimiento'])->name('inventario.movimiento.update');
    Route::delete('/inventario/movimiento/{movimiento}', [InventarioController::class, 'destroyMovimiento'])->name('inventario.movimiento.destroy');

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::put('/pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');

    // Reportes
    Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Mesas
    Route::resource('/mesas', \App\Http\Controllers\Admin\MesaController::class)->except(['show']);

    // Reservas
    Route::get('/reservas/plano', [\App\Http\Controllers\Admin\ReservaController::class, 'plano'])->name('reservas.plano');
    Route::get('/reservas/calendario', [\App\Http\Controllers\Admin\ReservaController::class, 'calendario'])->name('reservas.calendario');
    
    // AJAX Calendario
    Route::get('/reservas/eventos', [\App\Http\Controllers\Admin\ReservaController::class, 'eventos'])->name('reservas.eventos');
    Route::get('/reservas/metricas', [\App\Http\Controllers\Admin\ReservaController::class, 'metricas'])->name('reservas.metricas');
    Route::put('/reservas/{reserva}/drag', [\App\Http\Controllers\Admin\ReservaController::class, 'updateDrag'])->name('reservas.updateDrag');
    Route::get('/reservas/{reserva}/info', [\App\Http\Controllers\Admin\ReservaController::class, 'info'])->name('reservas.info');

    Route::put('/reservas/{reserva}/estado', [\App\Http\Controllers\Admin\ReservaController::class, 'cambiarEstado'])->name('reservas.cambiarEstado');
    Route::post('/reservas/{reserva}/pedido', [\App\Http\Controllers\Admin\ReservaController::class, 'convertirAPedido'])->name('reservas.convertirAPedido');
    Route::resource('/reservas', \App\Http\Controllers\Admin\ReservaController::class)->except(['show']);

    // Auditoría
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    
    // Opiniones
    Route::get('/opiniones', [\App\Http\Controllers\Admin\OpinionController::class, 'index'])->name('opiniones.index');
    Route::put('/opiniones/{opinion}/estado', [\App\Http\Controllers\Admin\OpinionController::class, 'cambiarEstado'])->name('opiniones.cambiarEstado');
    Route::post('/opiniones/{opinion}/responder', [\App\Http\Controllers\Admin\OpinionController::class, 'responder'])->name('opiniones.responder');
    Route::delete('/opiniones/{opinion}', [\App\Http\Controllers\Admin\OpinionController::class, 'destroy'])->name('opiniones.destroy');
});
