<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_reserva',
        'mesa_id',
        'user_id',
        'fecha',
        'hora',
        'personas',
        'ubicacion_preferida',
        'observaciones',
        'nombre_reserva',
        'telefono',
        'correo',
        'estado',
        'notificado_cliente',
        'motivo_estado',
        'admin_id',
        'fecha_resolucion'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'personas' => 'integer',
        'notificado_cliente' => 'boolean',
        'fecha_resolucion' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($reserva) {
            if (empty($reserva->numero_reserva)) {
                $reserva->numero_reserva = 'RES-' . strtoupper(uniqid());
            }
        });
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedido(): HasOne
    {
        return $this->hasOne(Pedido::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Helper para verificar conflicto
    public static function hasConflict($fecha, $hora, $mesa_id, $reserva_id = null)
    {
        $horaInicio = Carbon::parse($hora)->subHours(2)->format('H:i:s');
        $horaFin = Carbon::parse($hora)->addHours(2)->format('H:i:s');

        $query = self::where('mesa_id', $mesa_id)
            ->where('fecha', $fecha)
            ->whereBetween('hora', [$horaInicio, $horaFin])
            ->whereIn('estado', ['pendiente', 'confirmada']);
            
        if ($reserva_id) {
            $query->where('id', '!=', $reserva_id);
        }

        return $query->exists();
    }
}
