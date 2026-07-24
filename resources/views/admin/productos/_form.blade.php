<div class="row g-4">
    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre del Producto *</label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre) }}" required>
                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-semibold">Código Único *</label>
                <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo', $producto->codigo) }}" required>
                @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-semibold">Categoría *</label>
                <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                    <option value="" disabled {{ !$producto->categoria_id ? 'selected' : '' }}>Selecciona una categoría</option>
                    @foreach($categorias ?? \App\Models\Categoria::all() as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Precio ($) *</label>
                <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $producto->precio) }}" required min="0">
                @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Stock Inicial *</label>
                <input type="number" name="existencia" class="form-control @error('existencia') is-invalid @enderror" value="{{ old('existencia', $producto->existencia ?? 0) }}" required min="0" {{ $producto->exists ? 'readonly' : '' }}>
                @if($producto->exists)
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Modifica el stock desde Inventario.</div>
                @endif
                @error('existencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-12">
                <label class="form-label fw-semibold">Descripción (Opcional)</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-12 mt-4">
                <div class="form-check form-switch fs-5">
                    <input class="form-check-input" type="checkbox" role="switch" id="disponible" name="disponible" value="1" {{ old('disponible', $producto->disponible ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fs-6 mt-1 ms-2" for="disponible">Mostrar en el catálogo público</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <label class="form-label fw-semibold">Imagen del Producto</label>
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body text-center p-4">
                <img src="{{ asset($producto->imagen ?? 'images/no-image.png') }}" id="image-preview" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: contain;">
                
                <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                <div class="form-text mt-2">JPG, PNG o WEBP (Max 2MB). Relación 1:1 recomendada.</div>
                @error('imagen') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('imagen').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('image-preview').src = ev.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
