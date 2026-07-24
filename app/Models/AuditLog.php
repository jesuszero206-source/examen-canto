<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'accion', 'tabla', 'registro_id',
        'valores_anteriores', 'valores_nuevos',
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'valores_anteriores' => 'array',
            'valores_nuevos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper to create audit log entries
    public static function registrar(
        string $accion,
        string $tabla,
        ?int $registroId = null,
        ?array $anteriores = null,
        ?array $nuevos = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registroId,
            'valores_anteriores' => $anteriores,
            'valores_nuevos' => $nuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
