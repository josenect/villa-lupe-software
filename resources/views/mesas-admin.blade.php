@extends('layouts.app')

@section('title', 'Administrar Mesas - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-grid-3x3-gap"></i> Administrar Mesas
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

<!-- Formulario Nueva Mesa (ARRIBA) -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-plus-circle"></i> Nueva Mesa</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('admin.mesas.storeInTable') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label-custom">
                        <i class="bi bi-tag"></i> Nombre de la Mesa
                    </label>
                    <input type="text" name="name" class="form-control-custom"
                        placeholder="Ej: Mesa 1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label-custom">
                        <i class="bi bi-geo-alt"></i> Ubicación <small class="text-muted">(opcional)</small>
                    </label>
                    <input type="text" name="location" class="form-control-custom"
                        placeholder="Ej: Terraza, Interior…">
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

<!-- Buscador -->
<div class="d-flex align-items-center gap-3 mb-3 fade-in">
    <div style="position: relative; flex: 1; max-width: 380px;">
        <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#888;"></i>
        <input type="text" id="buscadorMesas" class="form-control-custom"
            placeholder="Buscar por nombre o ubicación..."
            style="padding-left: 36px;">
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary fs-6" title="Total">
            <i class="bi bi-grid-3x3-gap"></i> {{ $tables->count() }}
        </span>
        <span class="badge bg-success fs-6" title="Activas">
            <i class="bi bi-check-circle"></i> {{ $tables->where('status', 1)->count() }}
        </span>
        <span class="badge bg-secondary fs-6" title="Inactivas">
            <i class="bi bi-x-circle"></i> {{ $tables->where('status', 0)->count() }}
        </span>
    </div>
</div>

<!-- Tabla de Mesas -->
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-list-ul"></i> Mesas Registradas</h2>
    </div>
    <div class="card-body-custom">
        @if($tables->count() > 0)
            <div class="table-responsive">
                <table class="table-custom" id="tablaMesas">
                    <thead>
                        <tr>
                            <th><i class="bi bi-tag"></i> Nombre</th>
                            <th><i class="bi bi-geo-alt"></i> Ubicación</th>
                            <th><i class="bi bi-toggle-on"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaMesas">
                        @foreach ($tables as $mesa)
                            <tr class="fila-mesa"
                                data-nombre="{{ strtolower($mesa->name) }}"
                                data-ubicacion="{{ strtolower($mesa->location) }}">
                                <td data-label="Nombre">
                                    <strong><i class="bi bi-table text-primary"></i> {{ $mesa->name }}</strong>
                                </td>
                                <td data-label="Ubicación">{{ $mesa->location }}</td>
                                <td data-label="Estado">
                                    @if($mesa->status == 1)
                                        <span class="status-badge activo">
                                            <i class="bi bi-check-circle"></i> Activa
                                        </span>
                                    @else
                                        <span class="status-badge inactivo">
                                            <i class="bi bi-x-circle"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        <a class="btn-primary-custom btn-sm-custom"
                                            href="{{ route('admin.mesas.show', ['mesa_id' => $mesa->id]) }}"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a class="btn-danger-custom btn-sm-custom"
                                            href="{{ route('admin.mesas.delete', ['mesa_id' => $mesa->id]) }}"
                                            onclick="return confirm('¿Está seguro de eliminar {{ addslashes($mesa->name) }}?')"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="sinResultadosMesas" class="text-center text-muted mt-3" style="display:none;">
                    <i class="bi bi-search"></i> No se encontraron mesas con ese nombre o ubicación.
                </p>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay mesas registradas</p>
            </div>
        @endif
    </div>
</div>

<script>
document.getElementById('buscadorMesas')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('.fila-mesa');
    let visibles = 0;

    filas.forEach(function (fila) {
        const nombre    = fila.dataset.nombre    || '';
        const ubicacion = fila.dataset.ubicacion || '';
        const coincide  = nombre.includes(q) || ubicacion.includes(q);
        fila.style.display = coincide ? '' : 'none';
        if (coincide) visibles++;
    });

    document.getElementById('sinResultadosMesas').style.display = (visibles === 0 && q !== '') ? '' : 'none';
});
</script>
@endsection
