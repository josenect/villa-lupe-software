@extends('layouts.app')

@section('title', 'Editar Producto - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-pencil-square"></i> Editar Producto
    </h1>
    <a href="/admin/productos" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver a Productos
    </a>
</div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-custom alert-error-custom fade-in">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-box"></i> Actualizar: {{ $product->name }}</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.products.update', ['product_id' => $product->id]) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-tag"></i> Nombre del Producto
                        </label>
                        <input type="text" name="name" class="form-control-custom" value="{{ $product->name }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-folder"></i> Categoría
                        </label>
                        <select name="category" class="form-select-custom" required>
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->slug }}" {{ $product->category == $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->nombre }}{{ $cat->es_cocina ? ' 🔥' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-currency-dollar"></i> Precio
                        </label>
                        <input type="text" id="precioDisplay" class="form-control-custom"
                            value="{{ $product->price > 0 ? '$ ' . number_format($product->price, 0, ',', '.') : '' }}"
                            inputmode="numeric" autocomplete="off" required>
                        <input type="hidden" name="price" id="precioHidden" value="{{ $product->price }}">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-toggle-on"></i> Estado del Producto
                        </label>
                        <select name="status" class="form-select-custom">
                            <option value="1" {{ $product->status == "1" ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="0" {{ $product->status == "0" ? 'selected' : '' }}>
                                Inactivo
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-success-custom">
                            <i class="bi bi-check-lg"></i> Actualizar Producto
                        </button>
                        <a href="/admin/productos" class="btn-secondary-custom">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Info del Producto -->
        <div class="card-custom mt-4 fade-in">
            <div class="card-body-custom">
                <h5 class="section-title">
                    <i class="bi bi-info-circle"></i> Información Actual
                </h5>
                <div class="text-center p-3" style="background: rgba(52, 152, 219, 0.1); border-radius: 10px;">
                    <h3 class="text-primary mb-0">$ {{ number_format($product->price, 0, ',', '.') }}</h3>
                    <small class="text-muted">Precio Actual</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
const precioDisplay = document.getElementById('precioDisplay');
const precioHidden  = document.getElementById('precioHidden');

precioDisplay?.addEventListener('input', function () {
    const soloNumeros = this.value.replace(/\D/g, '');
    precioHidden.value = soloNumeros;
    this.value = soloNumeros
        ? '$ ' + Number(soloNumeros).toLocaleString('es-CO')
        : '';
});

precioDisplay?.addEventListener('keydown', function (e) {
    const permitidos = ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'];
    if (permitidos.includes(e.key)) return;
    if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
    if (e.key < '0' || e.key > '9') e.preventDefault();
});
</script>
@endsection
