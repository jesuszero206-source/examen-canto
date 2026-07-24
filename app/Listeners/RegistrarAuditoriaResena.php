<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Events\ResenaCreada;
use App\Events\ResenaActualizada;
use App\Events\ResenaEliminada;

class RegistrarAuditoriaResena
{
    public function handle(object $event): void
    {
        if (!isset($event->calificacion)) return;

        $action = 'Reseña gestionada';
        
        if ($event instanceof ResenaCreada) {
            $action = 'Crear Reseña';
        } elseif ($event instanceof ResenaActualizada) {
            $action = 'Actualizar Reseña';
        } elseif ($event instanceof ResenaEliminada) {
            $action = 'Eliminar Reseña';
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => 'Calificación ID: ' . $event->calificacion->id . ' en Producto ID: ' . $event->calificacion->producto_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
