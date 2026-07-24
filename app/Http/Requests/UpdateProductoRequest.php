<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $productoId = $this->route('producto');

        return [
            'categoria_id' => ['required', 'exists:categorias,id'],
            'codigo' => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo')->ignore($productoId)],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'existencia' => ['required', 'integer', 'min:0'],
            'disponible' => ['boolean'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'categoria_id.required' => 'Selecciona una categoría.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Este código ya está registrado por otro producto.',
            'nombre.required' => 'El nombre es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.min' => 'El precio debe ser mayor a $0.',
            'existencia.required' => 'La existencia es obligatoria.',
            'imagen.image' => 'El archivo debe ser una imagen.',
        ];
    }
}
