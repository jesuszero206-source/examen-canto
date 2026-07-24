<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->query('buscar');
        $categoriaActual = $request->query('categoria');

        $query = Producto::disponibles();

        if ($buscar) {
            $query->buscar($buscar);
        }

        if ($categoriaActual) {
            $query->porCategoria($categoriaActual);
        }

        $productos = $query->paginate(12)->withQueryString();
        $categorias = Categoria::all();

        return view('home.index', compact('productos', 'categorias', 'buscar', 'categoriaActual'));
    }
}
