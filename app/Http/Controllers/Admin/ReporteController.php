<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        $filtro = $request->query('filtro', '30_dias');
        $fechaInicio = null;
        $fechaFin = Carbon::now();

        switch ($filtro) {
            case 'hoy':
                $fechaInicio = Carbon::today();
                break;
            case 'semana':
                $fechaInicio = Carbon::now()->startOfWeek();
                break;
            case 'mes':
                $fechaInicio = Carbon::now()->startOfMonth();
                break;
            case '30_dias':
                $fechaInicio = Carbon::now()->subDays(30);
                break;
            case '90_dias':
                $fechaInicio = Carbon::now()->subDays(90);
                break;
            case 'rango':
                $fechaInicio = $request->query('fecha_inicio') ? Carbon::parse($request->query('fecha_inicio')) : Carbon::now()->subDays(30);
                $fechaFin = $request->query('fecha_fin') ? Carbon::parse($request->query('fecha_fin')) : Carbon::now();
                break;
            default:
                $fechaInicio = Carbon::now()->subDays(30);
                break;
        }

        // Tarjetas Superiores
        $ventasDia = Pedido::where('estado', '!=', 'cancelado')->whereDate('created_at', Carbon::today())->sum('total');
        $ventasMes = Pedido::where('estado', '!=', 'cancelado')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total');
        $ventasTotales = Pedido::where('estado', '!=', 'cancelado')->sum('total');
        
        $pedidosDia = Pedido::where('estado', '!=', 'cancelado')->whereDate('created_at', Carbon::today())->count();
        $pedidosMes = Pedido::where('estado', '!=', 'cancelado')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        
        $ticketPromedio = Pedido::where('estado', '!=', 'cancelado')->avg('total') ?? 0;

        $productoMasVendido = DetallePedido::join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->where('pedidos.estado', '!=', 'cancelado')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedidos.cantidad) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total')
            ->first();

        $clienteMasCompras = Pedido::where('estado', '!=', 'cancelado')
            ->join('users', 'pedidos.user_id', '=', 'users.id')
            ->select('users.nombre', 'users.apellido', DB::raw('SUM(pedidos.total) as gastado'))
            ->groupBy('users.id', 'users.nombre', 'users.apellido')
            ->orderByDesc('gastado')
            ->first();

        // -------------------------
        // NUEVOS REPORTES DE RESERVAS
        // -------------------------
        // Reservas por día (últimos 30 días)
        $reservasDiarias = \App\Models\Reserva::select(
            DB::raw('DATE(fecha) as fecha'),
            DB::raw('COUNT(*) as total_reservas'),
            DB::raw('SUM(personas) as total_personas')
        )
        ->where('fecha', '>=', Carbon::now()->subDays(30))
        ->groupBy('fecha')
        ->orderBy('fecha', 'asc')
        ->get();

        // Reservas por ubicación (histórico o últimos 30 días)
        $reservasPorUbicacion = \App\Models\Reserva::join('mesas', 'reservas.mesa_id', '=', 'mesas.id')
            ->select('mesas.ubicacion', DB::raw('COUNT(reservas.id) as total'))
            ->groupBy('mesas.ubicacion')
            ->get();

        // Mesas más utilizadas
        $mesasMasUtilizadas = \App\Models\Reserva::join('mesas', 'reservas.mesa_id', '=', 'mesas.id')
            ->select('mesas.numero', 'mesas.ubicacion', DB::raw('COUNT(reservas.id) as total_usos'))
            ->whereIn('reservas.estado', ['confirmada', 'finalizada'])
            ->groupBy('mesas.id', 'mesas.numero', 'mesas.ubicacion')
            ->orderByDesc('total_usos')
            ->take(5)
            ->get();

        // Horas con mayor ocupación
        $horasOcupacion = \App\Models\Reserva::select(
            DB::raw('HOUR(hora) as hora_reserva'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('hora_reserva')
        ->orderByDesc('total')
        ->take(5)
        ->get();

        // Reservas Canceladas vs Total (Mes actual)
        $reservasMesActual = \App\Models\Reserva::whereMonth('fecha', Carbon::now()->month)->count();
        $reservasCanceladasMes = \App\Models\Reserva::whereMonth('fecha', Carbon::now()->month)->where('estado', 'cancelada')->count();

        // 1. Ventas Diarias
        $ventasDiariasData = Pedido::where('estado', '!=', 'cancelado')
            ->when($fechaInicio, fn($q) => $q->whereDate('created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('created_at', '<=', $fechaFin))
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartVentasLabels = $ventasDiariasData->pluck('date')->toArray();
        $chartVentasData = $ventasDiariasData->pluck('total')->toArray();
        $chartPedidosData = $ventasDiariasData->pluck('cantidad')->toArray();

        // 2. Ingresos por Categoría
        $ingresosCategoria = DetallePedido::join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('pedidos.estado', '!=', 'cancelado')
            ->when($fechaInicio, fn($q) => $q->whereDate('pedidos.created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('pedidos.created_at', '<=', $fechaFin))
            ->selectRaw('categorias.nombre as categoria, SUM(detalle_pedidos.subtotal) as total')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->get();

        $chartCategoriasLabels = $ingresosCategoria->pluck('categoria')->toArray();
        $chartCategoriasData = $ingresosCategoria->pluck('total')->toArray();

        // 3. Productos más vendidios (Top 10)
        $topProductos = DetallePedido::join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('pedidos.estado', '!=', 'cancelado')
            ->when($fechaInicio, fn($q) => $q->whereDate('pedidos.created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('pedidos.created_at', '<=', $fechaFin))
            ->selectRaw('productos.nombre as producto, productos.imagen, productos.promedio_calificacion, productos.total_calificaciones, categorias.nombre as categoria, SUM(detalle_pedidos.cantidad) as unidades, SUM(detalle_pedidos.subtotal) as ingresos')
            ->groupBy('productos.id', 'productos.nombre', 'productos.imagen', 'productos.promedio_calificacion', 'productos.total_calificaciones', 'categorias.nombre')
            ->orderByDesc('unidades')
            ->limit(10)
            ->get();

        // 4. Estadísticas de Opiniones
        $totalOpiniones = \App\Models\Calificacion::when($fechaInicio, fn($q) => $q->whereDate('created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('created_at', '<=', $fechaFin))
            ->count();
            
        $promedioGeneral = \App\Models\Calificacion::when($fechaInicio, fn($q) => $q->whereDate('created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('created_at', '<=', $fechaFin))
            ->where('estado', 'visible')
            ->avg('calificacion') ?? 0;
            
        $opinionesPorEstrella = \App\Models\Calificacion::select(DB::raw('calificacion'), DB::raw('COUNT(*) as total'))
            ->when($fechaInicio, fn($q) => $q->whereDate('created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('created_at', '<=', $fechaFin))
            ->where('estado', 'visible')
            ->groupBy('calificacion')
            ->get();

        return view('admin.reportes.index', compact(
            'filtro', 'fechaInicio', 'fechaFin',
            'ventasDia', 'ventasMes', 'ventasTotales',
            'pedidosDia', 'pedidosMes', 'ticketPromedio',
            'productoMasVendido', 'clienteMasCompras',
            'chartVentasLabels', 'chartVentasData', 'chartPedidosData',
            'chartCategoriasLabels', 'chartCategoriasData',
            'topProductos',
            'reservasDiarias', 'reservasPorUbicacion', 'mesasMasUtilizadas',
            'horasOcupacion', 'reservasMesActual', 'reservasCanceladasMes',
            'totalOpiniones', 'promedioGeneral', 'opinionesPorEstrella'
        ));
    }

    public function exportar(Request $request)
    {
        $formato = $request->query('formato', 'csv');
        $filtro = $request->query('filtro', '30_dias');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now();

        switch ($filtro) {
            case 'hoy': $fechaInicio = Carbon::today(); break;
            case 'semana': $fechaInicio = Carbon::now()->startOfWeek(); break;
            case 'mes': $fechaInicio = Carbon::now()->startOfMonth(); break;
            case '30_dias': $fechaInicio = Carbon::now()->subDays(30); break;
            case '90_dias': $fechaInicio = Carbon::now()->subDays(90); break;
            case 'rango':
                $fechaInicio = $request->query('fecha_inicio') ? Carbon::parse($request->query('fecha_inicio')) : Carbon::now()->subDays(30);
                $fechaFin = $request->query('fecha_fin') ? Carbon::parse($request->query('fecha_fin')) : Carbon::now();
                break;
            default: $fechaInicio = Carbon::now()->subDays(30); break;
        }

        $pedidos = Pedido::with(['user', 'detalles.producto'])
            ->where('estado', '!=', 'cancelado')
            ->when($fechaInicio, fn($q) => $q->whereDate('created_at', '>=', $fechaInicio))
            ->when($fechaFin, fn($q) => $q->whereDate('created_at', '<=', $fechaFin))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($formato === 'pdf') {
            return view('admin.reportes.pdf', compact('pedidos', 'fechaInicio', 'fechaFin', 'filtro'));
        }

        // Export as CSV (also works for Excel if extension is csv)
        $fileName = 'Reporte_Ventas_' . Carbon::now()->format('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($pedidos) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Fecha', 'Cliente', 'Productos', 'Cantidad Total', 'Subtotal', 'IVA', 'Total', 'Metodo de Pago', 'Estado']);

            foreach ($pedidos as $pedido) {
                $nombresProductos = $pedido->detalles->map(function($detalle) {
                    return $detalle->producto->nombre . ' (x' . $detalle->cantidad . ')';
                })->implode(', ');
                
                $cantidadTotal = $pedido->detalles->sum('cantidad');
                $subtotal = $pedido->total / 1.16; // Assuming 16% IVA included
                $iva = $pedido->total - $subtotal;

                fputcsv($file, [
                    $pedido->created_at->format('Y-m-d H:i'),
                    $pedido->user->nombre_completo ?? 'Usuario Eliminado',
                    $nombresProductos,
                    $cantidadTotal,
                    number_format($subtotal, 2, '.', ''),
                    number_format($iva, 2, '.', ''),
                    number_format($pedido->total, 2, '.', ''),
                    $pedido->metodo_pago,
                    $pedido->estado
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
