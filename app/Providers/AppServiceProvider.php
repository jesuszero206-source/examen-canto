<?php

namespace App\Providers;

use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $carritoItemCount = 0;

            if (Auth::check()) {
                $carrito = Carrito::where('user_id', Auth::id())->first();
                if ($carrito) {
                    $carritoItemCount = $carrito->items()->sum('cantidad');
                }
            }

            $view->with('carritoItemCount', $carritoItemCount);
        });

        // Registrar Eventos de Calificaciones
        \Illuminate\Support\Facades\Event::listen(
            [\App\Events\ResenaCreada::class, \App\Events\ResenaActualizada::class, \App\Events\ResenaEliminada::class],
            \App\Listeners\RecalcularPromedioProducto::class
        );
        \Illuminate\Support\Facades\Event::listen(
            [\App\Events\ResenaCreada::class, \App\Events\ResenaActualizada::class, \App\Events\ResenaEliminada::class],
            \App\Listeners\RegistrarAuditoriaResena::class
        );
    }
}
