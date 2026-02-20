@extends('layouts.app')

@section('title', 'Administrar Productos - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-box-seam"></i> Administrar Productos
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categorias.index') }}" class="btn-secondary-custom">
            <i class="bi bi-tags"></i> Categorías
        </a>
        <a href="/" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Inicio
        </a>
    </div>
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

<!-- Formulario Nuevo Producto (ARRIBA) -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-plus-circle"></i> Nuevo Producto</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('admin.products.storeInTable') }}" method="POST" id="formNuevoProducto">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-custom">
                        <i class="bi bi-tag"></i> Nombre del Producto
                    </label>
                    <input type="text" name="name" class="form-control-custom"
                        placeholder="Ej: Almuerzo Ejecutivo" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label-custom">
                        <i class="bi bi-folder"></i> Categoría
                    </label>
                    <select name="category" class="form-select-custom" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->slug }}">
                                {{ $cat->nombre }}{{ $cat->es_cocina ? ' 🔥' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label-custom">
                        <i class="bi bi-currency-dollar"></i> Precio
                    </label>
                    <input type="text" id="precioDisplay" class="form-control-custom"
                        placeholder="$ 0" inputmode="numeric" autocomplete="off" required>
                    <input type="hidden" name="price" id="precioHidden">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn-success-custom w-100 justify-content-center">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Buscador (separado de la tabla) -->
<div class="d-flex align-items-center gap-3 mb-3 fade-in">
    <div style="position: relative; flex: 1; max-width: 380px;">
        <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#888;"></i>
        <input type="text" id="buscadorProductos" class="form-control-custom"
            placeholder="Buscar por nombre o categoría..."
            style="padding-left: 36px;">
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary fs-6" title="Total">
            <i class="bi bi-box-seam"></i> {{ $products->count() }}
        </span>
        <span class="badge bg-success fs-6" title="Activos">
            <i class="bi bi-check-circle"></i> {{ $products->where('status', 1)->count() }}
        </span>
        <span class="badge bg-secondary fs-6" title="Inactivos">
            <i class="bi bi-x-circle"></i> {{ $products->where('status', 0)->count() }}
        </span>
    </div>
</div>

<!-- Tabla de Productos -->
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-list-ul"></i> Productos Registrados</h2>
    </div>
    <div class="card-body-custom">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table-custom" id="tablaProductos">
                    <thead>
                        <tr>
                            <th><i class="bi bi-tag"></i> Nombre</th>
                            <th><i class="bi bi-folder"></i> Categoría</th>
                            <th><i class="bi bi-currency-dollar"></i> Precio</th>
                            <th><i class="bi bi-toggle-on"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaProductos">
                        @foreach ($products as $product)
                            <tr class="fila-producto"
                                data-nombre="{{ strtolower($product->name) }}"
                                data-categoria="{{ strtolower($product->category) }}">
                                <td data-label="Nombre">
                                    <strong><i class="bi bi-box text-primary"></i> {{ $product->name }}</strong>
                                </td>
                                <td data-label="Categoría">
                                    <span class="badge bg-info">{{ $product->category }}</span>
                                </td>
                                <td data-label="Precio">
                                    <strong class="text-success">$ {{ number_format($product->price, 0, ',', '.') }}</strong>
                                </td>
                                <td data-label="Estado">
                                    @if($product->status == 1)
                                        <span class="status-badge activo">
                                            <i class="bi bi-check-circle"></i> Activo
                                        </span>
                                    @else
                                        <span class="status-badge inactivo">
                                            <i class="bi bi-x-circle"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        <a class="btn-primary-custom btn-sm-custom"
                                            href="{{ route('admin.products.show', ['product_id' => $product->id]) }}"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a class="btn-danger-custom btn-sm-custom"
                                            href="{{ route('admin.products.delete', ['product_id' => $product->id]) }}"
                                            onclick="return confirm('¿Está seguro de eliminar {{ addslashes($product->name) }}?')"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="sinResultados" class="text-center text-muted mt-3" style="display:none;">
                    <i class="bi bi-search"></i> No se encontraron productos con ese nombre o categoría.
                </p>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay productos registrados</p>
            </div>
        @endif
    </div>
</div>

<script>
// ── Buscador en tiempo real ───────────────────────────────────────────────
document.getElementById('buscadorProductos')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('.fila-producto');
    let visibles = 0;

    filas.forEach(function (fila) {
        const nombre    = fila.dataset.nombre    || '';
        const categoria = fila.dataset.categoria || '';
        const coincide  = nombre.includes(q) || categoria.includes(q);
        fila.style.display = coincide ? '' : 'none';
        if (coincide) visibles++;
    });

    document.getElementById('sinResultados').style.display = (visibles === 0 && q !== '') ? '' : 'none';
});

// ── Formato de precio en el mismo input ──────────────────────────────────
const precioDisplay = document.getElementById('precioDisplay');
const precioHidden  = document.getElementById('precioHidden');

precioDisplay?.addEventListener('input', function () {
    const soloNumeros = this.value.replace(/\D/g, '');
    precioHidden.value = soloNumeros;
    this.value = soloNumeros
        ? '$ ' + Number(soloNumeros).toLocaleString('es-CO')
        : '';
});

// Impide escribir letras directamente
precioDisplay?.addEventListener('keydown', function (e) {
    const permitidos = ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'];
    if (permitidos.includes(e.key)) return;
    if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
    if (e.key < '0' || e.key > '9') e.preventDefault();
});

// Valida antes de enviar
document.getElementById('formNuevoProducto')?.addEventListener('submit', function (e) {
    if (!precioHidden.value) {
        e.preventDefault();
        precioDisplay.focus();
        precioDisplay.style.borderColor = '#e74c3c';
    }
});
</script>
@endsection
