<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Reserva;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservaClienteController extends Controller
{
    public function index(Request $request): View
    {
        $ubicaciones = ['interior', 'terraza', 'balcon', 'jardin', 'ventana'];
        
        return view('reservas.index', compact('ubicaciones'));
    }

    // Eliminar checkDisponibilidad según el requerimiento.
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'personas' => 'required|integer|min:1',
            'nombre_reserva' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'nullable|email|max:255'
        ]);

        // Evitar reservas duplicadas para el mismo usuario en la misma fecha y hora
        $existeDuplicada = Reserva::where('user_id', Auth::id())
            ->where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->exists();
            
        if ($existeDuplicada) {
            return back()->with('error', 'Ya tienes una reserva para esta fecha y hora.')->withInput();
        }

        try {
            DB::beginTransaction();

            $reserva = Reserva::create([
                'user_id' => Auth::id(),
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'personas' => $request->personas,
                'ubicacion_preferida' => $request->ubicacion_preferida,
                'observaciones' => $request->observaciones,
                'nombre_reserva' => $request->nombre_reserva,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'estado' => 'pendiente'
            ]);

            // Auditoría
            AuditLog::registrar('create', 'reservas', $reserva->id, null, $reserva->toArray());

            DB::commit();

            return redirect()->route('perfil.reservas.index')->with('success', "Tu solicitud fue enviada correctamente. Un administrador revisará tu solicitud.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al procesar tu reserva.')->withInput();
        }
    }

    public function misReservas()
    {
        $reservas = Reserva::where('user_id', Auth::id())
            ->with('mesa', 'admin')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
            
        // Marcar todas como notificadas al entrar
        Reserva::where('user_id', Auth::id())
            ->where('notificado_cliente', false)
            ->update(['notificado_cliente' => true]);

        return view('reservas.mis_reservas', compact('reservas'));
    }

    public function cancelar(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            return back()->with('error', 'No puedes cancelar esta reserva.');
        }

        // Regla: Cancelar solo si faltan más de X horas (ej. 24h o 2h, asumiremos 2h)
        $fechaHoraReserva = Carbon::parse($reserva->fecha->format('Y-m-d') . ' ' . $reserva->hora->format('H:i:s'));
        if (now()->diffInHours($fechaHoraReserva, false) < 2) {
            return back()->with('error', 'No puedes cancelar con menos de 2 horas de anticipación.');
        }

        try {
            DB::beginTransaction();
            $reserva->update([
                'estado' => 'cancelada',
                'motivo_estado' => 'Cancelada por el cliente',
                'notificado_cliente' => true
            ]);
            AuditLog::registrar('update', 'reservas', $reserva->id, null, $reserva->toArray());
            DB::commit();
            return back()->with('success', 'Tu reserva ha sido cancelada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al cancelar.');
        }
    }

    public function marcarNotificacionLeida(Request $request)
    {
        Reserva::where('user_id', Auth::id())
            ->where('notificado_cliente', false)
            ->update(['notificado_cliente' => true]);
            
        return response()->json(['success' => true]);
    }
}
