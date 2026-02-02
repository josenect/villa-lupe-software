@extends('layouts.app')

@section('title', 'Administrar Productos - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-box-seam"></i> Administrar Productos
    </h1>
    <a href="/" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver al Inicio
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

<div class="row g-4">
    <!-- Tabla de Productos -->
    <div class="col-lg-8">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-list-ul"></i> Productos Registrados</h2>
            </div>
            <div class="card-body-custom">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-tag"></i> Nombre</th>
                                    <th><i class="bi bi-folder"></i> Categoría</th>
                                    <th><i class="bi bi-currency-dollar"></i> Precio</th>
                                    <th><i class="bi bi-boxes"></i> Inventario</th>
                                    <th><i class="bi bi-toggle-on"></i> Estado</th>
                                    <th><i class="bi bi-gear"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td data-label="Nombre">
                                            <strong><i class="bi bi-box text-primary"></i> {{ $product->name }}</strong>
                                        </td>
                                        <td data-label="Categoría">
                                            <span class="badge bg-info">{{ $product->category }}</span>
                                        </td>
                                        <td data-label="Precio">
                                            <strong class="text-success">$ {{ number_format($product->price, 0, ',', '.') }}</strong>
                                        </td>
                                        <td data-label="Inventario">
                                            <span class="badge {{ $product->inventory > 10 ? 'bg-success' : ($product->inventory > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $product->inventory }}
                                            </span>
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
                                                <a class="btn-primary-custom btn-sm-custom" href="{{ route('admin.products.show', ['product_id' => $product->id]) }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a class="btn-danger-custom btn-sm-custom" href="{{ route('admin.products.delete', ['product_id' => $product->id]) }}" onclick="return confirm('¿Está seguro de eliminar este producto?')" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">No hay productos registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Formulario Agregar Producto -->
    <div class="col-lg-4">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-plus-circle"></i> Nuevo Producto</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.products.storeInTable') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-tag"></i> Nombre del Producto
                        </label>
                        <input type="text" name="name" class="form-control-custom" placeholder="Ej: Almuerzo Ejecutivo" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-folder"></i> Categoría
                        </label>
                        <select name="category" class="form-select-custom" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="restaurante-almuerzos">Restaurante Almuerzos</option>
                            <option value="restaurante-bebida">Restaurante Bebidas</option>
                            <option value="restaurante-adicional">Restaurante Adicional</option>
                            <option value="caseta">Caseta</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-currency-dollar"></i> Precio
                        </label>
                        <input type="number" name="price" class="form-control-custom" placeholder="0" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-boxes"></i> Inventario Inicial
                        </label>
                        <input type="number" name="inventory" class="form-control-custom" placeholder="0" min="0">
                    </div>
                    
                    <button type="submit" class="btn-success-custom w-100 justify-content-center mt-3">
                        <i class="bi bi-plus-lg"></i> Agregar Producto
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="card-custom mt-4 fade-in">
            <div class="card-body-custom">
                <h5 class="section-title">
                    <i class="bi bi-bar-chart"></i> Resumen
                </h5>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Productos</span>
                    <span class="badge bg-primary fs-6">{{ $products->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Productos Activos</span>
                    <span class="badge bg-success fs-6">{{ $products->where('status', 1)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Productos Inactivos</span>
                    <span class="badge bg-secondary fs-6">{{ $products->where('status', 0)->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
