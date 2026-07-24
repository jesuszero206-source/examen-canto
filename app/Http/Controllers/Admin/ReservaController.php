<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Reserva::with(['mesa', 'user']);

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }
        if ($request->fecha) {
            $query->where('fecha', $request->fecha);
        }
        if ($request->buscar) {
            $query->where(function($q) use ($request) {
                $q->where('numero_reserva', 'like', "%{$request->buscar}%")
                  ->orWhere('nombre_reserva', 'like', "%{$request->buscar}%");
            });
        }

        $reservas = $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->paginate(15);
        $mesas = Mesa::where('estado', 'disponible')->where('activa', true)->get();
        return view('admin.reservas.index', compact('reservas', 'mesas'));
    }

    public function plano(): View
    {
        $mesas = Mesa::with(['reservas' => function($q) {
            $q->where('fecha', '>=', Carbon::today())
              ->whereIn('estado', ['pendiente', 'confirmada'])
              ->orderBy('fecha')->orderBy('hora');
        }])->get();
        return view('admin.reservas.plano', compact('mesas'));
    }

    public function calendario(Request $request): View
    {
        $mesas = Mesa::where('activa', true)->get();
        return view('admin.reservas.calendario', compact('mesas'));
    }

    // --- AJAX Endpoints for Calendar ---

    public function eventos(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end) : Carbon::now()->endOfMonth();

        $query = Reserva::with(['mesa', 'user'])
            ->whereBetween('fecha', [$start->format('Y-m-d'), $end->format('Y-m-d')]);

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $reservas = $query->get();

        $eventos = $reservas->map(function($r) {
            $color = match($r->estado) {
                'pendiente' => '#ffc107',
                'confirmada' => '#198754',
                'rechazada' => '#dc3545',
                'finalizada' => '#0dcaf0',
                'cancelada' => '#6c757d',
                default => '#6c757d'
            };
            
            return [
                'id' => $r->id,
                'title' => $r->nombre_reserva . ' (' . $r->personas . ' p)',
                'start' => $r->fecha->format('Y-m-d') . 'T' . $r->hora->format('H:i:s'),
                'color' => $color,
                'extendedProps' => [
                    'numero_reserva' => $r->numero_reserva,
                    'estado' => $r->estado,
                    'personas' => $r->personas,
                    'mesa_id' => $r->mesa_id,
                    'mesa_numero' => $r->mesa ? $r->mesa->numero : 'Sin asignar',
                    'telefono' => $r->telefono,
                    'correo' => $r->correo,
                    'observaciones' => $r->observaciones
                ]
            ];
        });

        return response()->json($eventos);
    }

    public function metricas(Request $request)
    {
        // Métricas básicas (hoy o globales, usaremos globales o del mes actual dependiendo del requerimiento, pero hagamos globales/hoy)
        $hoy = Carbon::today();
        
        $total = Reserva::count();
        $pendientes = Reserva::where('estado', 'pendiente')->count();
        $confirmadas = Reserva::where('estado', 'confirmada')->count();
        $rechazadas = Reserva::where('estado', 'rechazada')->count();
        $canceladas = Reserva::where('estado', 'cancelada')->count();
        $finalizadas = Reserva::where('estado', 'finalizada')->count();
        
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $mesasDisponibles = Mesa::where('estado', 'disponible')->count();

        return response()->json([
            'total' => $total,
            'pendientes' => $pendientes,
            'confirmadas' => $confirmadas,
            'rechazadas' => $rechazadas,
            'canceladas' => $canceladas,
            'finalizadas' => $finalizadas,
            'mesas_ocupadas' => $mesasOcupadas,
            'mesas_disponibles' => $mesasDisponibles
        ]);
    }

    public function info(Reserva $reserva)
    {
        $reserva->load(['mesa', 'user']);
        
        // Obtener historial de auditoría para esta reserva
        $historial = AuditLog::with('user')
            ->where('table_name', 'reservas')
            ->where('record_id', $reserva->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reserva' => $reserva,
            'historial' => $historial
        ]);
    }

    public function updateDrag(Request $request, Reserva $reserva)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required'
        ]);

        $nuevaFecha = $request->fecha;
        $nuevaHora = $request->hora;
        $mesa_id = $request->mesa_id ?? $reserva->mesa_id;

        // Validar no fechas en el pasado (si es requerido, por ahora solo validamos el conflicto real)
        if (Carbon::parse("$nuevaFecha $nuevaHora")->isPast() && $reserva->estado !== 'finalizada' && $reserva->estado !== 'cancelada') {
            return response()->json(['success' => false, 'message' => 'No se pueden mover reservas a una fecha pasada.'], 422);
        }

        if (in_array($reserva->estado, ['finalizada', 'cancelada', 'rechazada'])) {
            return response()->json(['success' => false, 'message' => 'No se puede mover una reserva inactiva.'], 422);
        }

        if ($mesa_id && Reserva::hasConflict($nuevaFecha, $nuevaHora, $mesa_id, $reserva->id)) {
            return response()->json(['success' => false, 'message' => 'Conflicto de horario: La mesa ya está ocupada en ese momento.'], 422);
        }

        try {
            DB::beginTransaction();
            $anteriores = $reserva->toArray();
            
            $reserva->fecha = $nuevaFecha;
            $reserva->hora = $nuevaHora;
            if ($request->has('mesa_id')) {
                $reserva->mesa_id = $mesa_id;
            }
            $reserva->save();

            AuditLog::registrar('drag_drop', 'reservas', $reserva->id, $anteriores, $reserva->toArray());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Reserva reprogramada exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error interno al mover reserva.'], 500);
        }
    }
    
    // --- Fin AJAX Endpoints ---

    public function create(): View
    {
        $mesas = Mesa::where('activa', true)->get();
        return view('admin.reservas.create', compact('mesas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'personas' => 'required|integer|min:1',
            'nombre_reserva' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,confirmada,finalizada,cancelada,rechazada'
        ]);

        if (Reserva::hasConflict($request->fecha, $request->hora, $request->mesa_id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Conflicto de horario para la mesa seleccionada.'], 422);
            }
            return back()->with('error', 'Conflicto de horario para la mesa seleccionada.')->withInput();
        }

        try {
            DB::beginTransaction();
            $reserva = Reserva::create($request->all());
            AuditLog::registrar('create', 'reservas', $reserva->id, null, $reserva->toArray());
            DB::commit();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Reserva creada exitosamente.', 'reserva' => $reserva]);
            }
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error interno al crear reserva.'], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Reserva $reserva): View
    {
        $mesas = Mesa::where('activa', true)->get();
        return view('admin.reservas.edit', compact('reserva', 'mesas'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'personas' => 'required|integer|min:1',
            'nombre_reserva' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,confirmada,finalizada,cancelada,rechazada'
        ]);

        if (Reserva::hasConflict($request->fecha, $request->hora, $request->mesa_id, $reserva->id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Conflicto de horario para la mesa seleccionada.'], 422);
            }
            return back()->with('error', 'Conflicto de horario para la mesa seleccionada.')->withInput();
        }

        try {
            DB::beginTransaction();
            $anteriores = $reserva->toArray();
            $reserva->update($request->all());
            AuditLog::registrar('update', 'reservas', $reserva->id, $anteriores, $reserva->toArray());
            DB::commit();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Reserva actualizada exitosamente.', 'reserva' => $reserva]);
            }
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error interno al actualizar reserva.'], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function cambiarEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,rechazada,cancelada,finalizada',
            'motivo_estado' => 'nullable|string|max:500',
            'mesa_id' => 'nullable|exists:mesas,id'
        ]);

        try {
            DB::beginTransaction();
            
            $anteriores = $reserva->toArray();

            $updates = [
                'estado' => $request->estado,
                'admin_id' => Auth::id(),
                'fecha_resolucion' => now(),
                'notificado_cliente' => false // Trigger notification
            ];

            if ($request->estado === 'rechazada' && $request->motivo_estado) {
                $updates['motivo_estado'] = $request->motivo_estado;
            }
            
            if ($request->estado === 'confirmada') {
                $mesa_id = $request->mesa_id ?? $reserva->mesa_id;
                if (!$mesa_id) {
                    throw new \Exception('Debe seleccionar una mesa para confirmar la reserva.');
                }
                
                // Verificar conflicto nuevamente por seguridad
                if (Reserva::hasConflict($reserva->fecha, $reserva->hora, $mesa_id, $reserva->id)) {
                    throw new \Exception('La mesa seleccionada ya no está disponible en este horario.');
                }
                
                $updates['mesa_id'] = $mesa_id;
                
                // Cambiar estado de la mesa
                $mesa = Mesa::find($mesa_id);
                if ($mesa && $mesa->estado === 'disponible') {
                    $mesaAnterior = $mesa->toArray();
                    $mesa->estado = 'reservada';
                    $mesa->save();
                    AuditLog::registrar('cambio_estado_mesa', 'mesas', $mesa->id, $mesaAnterior, $mesa->toArray());
                }
            }

            // Liberar mesa si se cancela o finaliza
            if (in_array($request->estado, ['cancelada', 'finalizada', 'rechazada'])) {
                if ($reserva->mesa_id) {
                    $mesa = Mesa::find($reserva->mesa_id);
                    if ($mesa && $mesa->estado === 'reservada') {
                        $mesaAnterior = $mesa->toArray();
                        $mesa->estado = 'disponible';
                        $mesa->save();
                        AuditLog::registrar('cambio_estado_mesa', 'mesas', $mesa->id, $mesaAnterior, $mesa->toArray());
                    }
                }
            }

            $reserva->update($updates);

            AuditLog::registrar('update_estado', 'reservas', $reserva->id, $anteriores, $reserva->toArray());

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Estado actualizado exitosamente.']);
            }
            return back()->with('success', 'Estado de la reserva actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function convertirAPedido(Reserva $reserva)
    {
        if ($reserva->estado === 'finalizada' || $reserva->estado === 'cancelada') {
            return back()->with('error', 'La reserva ya no está activa.');
        }

        try {
            DB::beginTransaction();
            
            // 1. Convert to Pedido
            $pedido = Pedido::create([
                'user_id' => $reserva->user_id ?? Auth::id(), // Guest -> assign to current admin
                'estado' => 'pendiente',
                'metodo_pago' => 'efectivo', // default
                'subtotal' => 0,
                'impuesto' => 0,
                'total' => 0,
                'notas' => 'Pedido generado desde Reserva ' . $reserva->numero_reserva,
                'reserva_id' => $reserva->id,
                'mesa_id' => $reserva->mesa_id
            ]);

            // 2. Change mesa status
            $mesa = $reserva->mesa;
            if ($mesa) {
                $anterioresMesa = $mesa->toArray();
                $mesa->estado = 'ocupada';
                $mesa->save();
                AuditLog::registrar('cambio_estado_mesa', 'mesas', $mesa->id, $anterioresMesa, $mesa->toArray());
            }

            // 3. Mark reserva as finalizada
            $anterioresReserva = $reserva->toArray();
            $reserva->estado = 'finalizada';
            $reserva->save();
            
            AuditLog::registrar('conversion_pedido', 'reservas', $reserva->id, $anterioresReserva, $reserva->toArray());

            DB::commit();
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva convertida a Pedido exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al convertir: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Reserva $reserva)
    {
        try {
            DB::beginTransaction();
            $anteriores = $reserva->toArray();
            
            // Si la reserva estaba asignada y la mesa reservada, liberarla
            if ($reserva->mesa_id) {
                $mesa = Mesa::find($reserva->mesa_id);
                if ($mesa && $mesa->estado === 'reservada') {
                    $mesaAnterior = $mesa->toArray();
                    $mesa->estado = 'disponible';
                    $mesa->save();
                    AuditLog::registrar('cambio_estado_mesa', 'mesas', $mesa->id, $mesaAnterior, $mesa->toArray());
                }
            }
            
            $reserva->delete();
            AuditLog::registrar('delete', 'reservas', $reserva->id, $anteriores, null);
            DB::commit();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Reserva eliminada exitosamente.']);
            }
            return back()->with('success', 'Reserva eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar la reserva.'], 500);
            }
            return back()->with('error', 'Error al eliminar la reserva.');
        }
    }
}
