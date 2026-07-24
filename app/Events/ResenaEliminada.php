<?php

namespace App\Events;

use App\Models\Calificacion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResenaEliminada
{
    use Dispatchable, SerializesModels;

    public Calificacion $calificacion;

    public function __construct(Calificacion $calificacion)
    {
        $this->calificacion = $calificacion;
    }
}
