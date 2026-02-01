@extends('layouts.app')

@section('title', 'Editar Mesa - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-pencil-square"></i> Editar Mesa
    </h1>
    <a href="/admin/mesas" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver a Mesas
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
                <h2><i class="bi bi-table"></i> Actualizar Mesa: {{ $table->name }}</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.mesas.update', ['mesa_id' => $table->id]) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-tag"></i> Nombre de la Mesa
                        </label>
                        <input type="text" name="name" class="form-control-custom" value="{{ $table->name }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-geo-alt"></i> Ubicación
                        </label>
                        <input type="text" name="location" class="form-control-custom" value="{{ $table->location }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-toggle-on"></i> Estado de la Mesa
                        </label>
                        <select name="status" class="form-select-custom">
                            <option value="1" {{ $table->status == "1" ? 'selected' : '' }}>
                                Activa
                            </option>
                            <option value="0" {{ $table->status == "0" ? 'selected' : '' }}>
                                Inactiva
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-success-custom">
                            <i class="bi bi-check-lg"></i> Actualizar Mesa
                        </button>
                        <a href="/admin/mesas" class="btn-secondary-custom">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
