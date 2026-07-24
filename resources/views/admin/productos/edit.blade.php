@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('actions')
<a href="{{ route('admin.productos.index') }}" class="btn btn-light shadow-sm">
    <i class="bi bi-arrow-left me-1"></i> Volver a Productos
</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
        <h5 class="fw-bold mb-0">Editando: {{ $producto->nombre }}</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @include('admin.productos._form', ['producto' => $producto])
            
            <div class="text-end mt-5 border-top pt-4">
                <a href="{{ route('admin.productos.index') }}" class="btn btn-light me-2 px-4">Cancelar</a>
                <button type="submit" class="btn btn-cafe px-4">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
