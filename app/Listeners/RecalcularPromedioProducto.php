<?php

namespace App\Listeners;

use App\Services\CalificacionService;

class RecalcularPromedioProducto
{
    protected CalificacionService $calificacionService;

    public function __construct(CalificacionService $calificacionService)
    {
        $this->calificacionService = $calificacionService;
    }

    public function handle(object $event): void
    {
        if (isset($event->calificacion) && $event->calificacion->producto_id) {
            $this->calificacionService->recalcularPromedioProducto($event->calificacion->producto_id);
        }
    }
}
