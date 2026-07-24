@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast toast-cafe toast-success align-items-center text-bg-light border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast toast-cafe toast-error align-items-center text-bg-light border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast toast-cafe toast-error align-items-center text-bg-light border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                Por favor, corrige los errores en el formulario.
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif
