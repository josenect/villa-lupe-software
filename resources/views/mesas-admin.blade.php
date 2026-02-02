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

<div class="row g-4">
    <!-- Tabla de Mesas -->
    <div class="col-lg-8">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-list-ul"></i> Mesas Registradas</h2>
            </div>
            <div class="card-body-custom">
                @if($tables->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-tag"></i> Nombre</th>
                                    <th><i class="bi bi-geo-alt"></i> Ubicación</th>
                                    <th><i class="bi bi-toggle-on"></i> Estado</th>
                                    <th><i class="bi bi-gear"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tables as $mesa)
                                    <tr>
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
                                                <a class="btn-primary-custom btn-sm-custom" href="{{ route('admin.mesas.show', ['mesa_id' => $mesa->id]) }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a class="btn-danger-custom btn-sm-custom" href="{{ route('admin.mesas.delete', ['mesa_id' => $mesa->id]) }}" onclick="return confirm('¿Está seguro de eliminar esta mesa?')" title="Eliminar">
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
                        <p class="text-muted mt-3">No hay mesas registradas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Formulario Agregar Mesa -->
    <div class="col-lg-4">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-plus-circle"></i> Nueva Mesa</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.mesas.storeInTable') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-tag"></i> Nombre de la Mesa
                        </label>
                        <input type="text" name="name" class="form-control-custom" placeholder="Ej: Mesa 1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-geo-alt"></i> Ubicación
                        </label>
                        <input type="text" name="location" class="form-control-custom" placeholder="Ej: Terraza, Interior, Jardín" required>
                    </div>
                    
                    <button type="submit" class="btn-success-custom w-100 justify-content-center mt-3">
                        <i class="bi bi-plus-lg"></i> Agregar Mesa
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
                    <span class="text-muted">Total de Mesas</span>
                    <span class="badge bg-primary fs-6">{{ $tables->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Mesas Activas</span>
                    <span class="badge bg-success fs-6">{{ $tables->where('status', 1)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Mesas Inactivas</span>
                    <span class="badge bg-secondary fs-6">{{ $tables->where('status', 0)->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
