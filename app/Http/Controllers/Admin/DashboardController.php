<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoy = \Carbon\Carbon::today();
        $inicioMes = \Carbon\Carbon::now()->startOfMonth();

        // TARJETA 1: PEDIDOS TOTALES
        $totalPedidos = Pedido::count();

        // TARJETA 2: INGRESOS
        $ingresosTotales = Pedido::where('estado', '!=', 'cancelado')->sum('total');
        $ingresosMes = Pedido::where('estado', '!=', 'cancelado')->where('created_at', '>=', $inicioMes)->sum('total');
        $ingresosHoy = Pedido::where('estado', '!=', 'cancelado')->whereDate('created_at', $hoy)->sum('total');

        // TARJETA 3: PRODUCTOS ACTIVOS
        $totalProductos = Producto::where('disponible', true)->count();

        // TARJETA 4: USUARIOS REGISTRADOS (solo clientes)
        $totalUsuarios = User::whereHas('roles', function($q) {
            $q->where('name', 'Cliente');
        })->count();
        
        // ÚLTIMOS PEDIDOS
        $ultimosPedidos = Pedido::with('user')->orderBy('created_at', 'desc')->take(10)->get();
        
        // STOCK BAJO
        $stockBajo = Producto::where('existencia', '<=', 5)->get();
        
        // ESTADÍSTICAS ADICIONALES
        $productoMasVendido = DetallePedido::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->with('producto')
            ->first();
            
        $clienteEstrella = Pedido::where('estado', '!=', 'cancelado')
            ->select('user_id', DB::raw('COUNT(*) as total_pedidos'), DB::raw('SUM(total) as total_gastado'))
            ->groupBy('user_id')
            ->orderBy('total_pedidos', 'desc')
            ->with('user')
            ->first();

        $inventarioBajo = Producto::where('existencia', '<=', 5)->count();

        $productosStockBajo = Producto::where('existencia', '<=', 5)
            ->where('disponible', true)
            ->get();

        $ultimosPedidos = Pedido::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Reservas & Mesas Metrics
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        $reservasHoy = \App\Models\Reserva::whereDate('fecha', $hoy)->count();
        $reservasPendientes = \App\Models\Reserva::where('estado', 'pendiente')->count();
        $reservasConfirmadas = \App\Models\Reserva::where('estado', 'confirmada')->count();
        $reservasRechazadas = \App\Models\Reserva::where('estado', 'rechazada')->count();
            
        $reservasSemana = \App\Models\Reserva::whereBetween('fecha', [$inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d')])
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->count();

        $mesasTotales = \App\Models\Mesa::where('activa', true)->count();
        $mesasOcupadas = \App\Models\Mesa::where('estado', 'ocupada')->where('activa', true)->count();
        $mesasDisponibles = \App\Models\Mesa::where('estado', 'disponible')->where('activa', true)->count();
        $mesasReservadas = \App\Models\Mesa::where('estado', 'reservada')->where('activa', true)->count();
        
        $mesasNoDisponibles = $mesasOcupadas + $mesasReservadas;
        $porcentajeOcupacion = $mesasTotales > 0 ? round(($mesasNoDisponibles / $mesasTotales) * 100) : 0;

        $proximasReservas = \App\Models\Reserva::with('mesa')
            ->where('fecha', '>=', $hoy->format('Y-m-d'))
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->take(5)
            ->get();

        $promedioPedido = Pedido::where('estado', '!=', 'cancelado')->avg('total') ?? 0;
        $totalProductosVendidos = DetallePedido::whereHas('pedido', function($q) {
            $q->where('estado', '!=', 'cancelado');
        })->sum('cantidad');
        
        // Opiniones Metrics
        $opinionesPendientes = \App\Models\Calificacion::where('estado', 'reportado')->count();
        $opinionesTotales = \App\Models\Calificacion::count();

        return view('admin.dashboard.index', compact(
            'totalProductos',
            'totalUsuarios',
            'totalPedidos',
            'ingresosTotales',
            'ingresosMes',
            'ingresosHoy',
            'ultimosPedidos',
            'productosStockBajo',
            'inventarioBajo',
            'productoMasVendido',
            'clienteEstrella',
            'promedioPedido',
            'totalProductosVendidos',
            'reservasHoy',
            'reservasPendientes',
            'reservasConfirmadas',
            'reservasRechazadas',
            'reservasSemana',
            'mesasTotales',
            'mesasOcupadas',
            'mesasDisponibles',
            'mesasReservadas',
            'porcentajeOcupacion',
            'proximasReservas',
            'opinionesPendientes',
            'opinionesTotales'
        ));
    }
}
