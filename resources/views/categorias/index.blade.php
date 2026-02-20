@extends('layouts.app')

@section('title', 'Categorías - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-tags"></i> Categorías
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

<div class="row g-4">
    <!-- Lista de categorías -->
    <div class="col-lg-8">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-list-ul"></i> Categorías Registradas</h2>
            </div>
            <div class="card-body-custom">
                @if($categorias->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-tag"></i> Nombre</th>
                                    <th><i class="bi bi-link-45deg"></i> Slug</th>
                                    <th><i class="bi bi-fire"></i> Cocina</th>
                                    <th><i class="bi bi-toggle-on"></i> Estado</th>
                                    <th><i class="bi bi-gear"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                <tr>
                                    <td><strong>{{ $categoria->nombre }}</strong></td>
                                    <td><code>{{ $categoria->slug }}</code></td>
                                    <td>
                                        @if($categoria->es_cocina)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-fire"></i> Sí
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($categoria->activo)
                                            <span class="status-badge activo">
                                                <i class="bi bi-check-circle"></i> Activa
                                            </span>
                                        @else
                                            <span class="status-badge inactivo">
                                                <i class="bi bi-x-circle"></i> Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-primary-custom btn-sm-custom"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $categoria->id }}"
                                                title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.categorias.toggle', $categoria->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-warning-custom btn-sm-custom"
                                                    title="{{ $categoria->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="bi bi-{{ $categoria->activo ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" style="display:inline;"
                                                onsubmit="return confirm('¿Eliminar la categoría {{ $categoria->nombre }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger-custom btn-sm-custom" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">No hay categorías registradas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulario nueva categoría -->
    <div class="col-lg-4">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-plus-circle"></i> Nueva Categoría</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.categorias.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label-custom">
                            <i class="bi bi-tag"></i> Nombre
                        </label>
                        <input type="text" name="nombre" class="form-control-custom"
                            placeholder="Ej: Bebidas Especiales" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="es_cocina" id="esCocinaNew">
                        <label class="form-check-label" for="esCocinaNew">
                            <i class="bi bi-fire"></i> Aparece en vista de Cocina
                        </label>
                    </div>
                    <button type="submit" class="btn-success-custom w-100 justify-content-center">
                        <i class="bi bi-plus-lg"></i> Crear Categoría
                    </button>
                </form>
            </div>
        </div>

        <div class="card-custom mt-4 fade-in">
            <div class="card-body-custom">
                <h5 class="section-title"><i class="bi bi-info-circle"></i> ¿Qué es Cocina?</h5>
                <p class="text-muted small">
                    Las categorías marcadas como <strong>Cocina</strong> se muestran en la pantalla de cocina
                    y en la sección "Cocina" del mesero. Las demás aparecen solo en la sección "Otros".
                </p>
                <p class="text-muted small mt-2">
                    El <strong>slug</strong> se genera automáticamente del nombre y no puede cambiarse.
                    Debe coincidir con la categoría asignada a los productos.
                </p>
            </div>
        </div>
    </div>
</div>
{{-- Modales de edición (fuera de la tabla para evitar conflictos de z-index) --}}
@foreach ($categorias as $categoria)
<div class="modal fade" id="editModal{{ $categoria->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $categoria->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px; border:none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50, #3498db); color:white; border-radius:16px 16px 0 0;">
                <h5 class="modal-title" id="editModalLabel{{ $categoria->id }}">
                    <i class="bi bi-pencil-square"></i> Editar: {{ $categoria->nombre }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label-custom">Nombre</label>
                        <input type="text" name="nombre" class="form-control-custom"
                            value="{{ $categoria->nombre }}" required>
                        <small class="text-muted">El slug (<code>{{ $categoria->slug }}</code>) no cambia al editar el nombre.</small>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="es_cocina"
                            id="esCocina{{ $categoria->id }}"
                            {{ $categoria->es_cocina ? 'checked' : '' }}>
                        <label class="form-check-label" for="esCocina{{ $categoria->id }}">
                            <i class="bi bi-fire"></i> Aparece en vista de Cocina
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-success-custom">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
