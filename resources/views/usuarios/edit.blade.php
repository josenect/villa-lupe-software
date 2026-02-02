@extends('layouts.app')

@section('title', 'Editar Usuario - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-person-gear"></i> Editar Usuario
    </h1>
    <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-pencil"></i> Editando: {{ $usuario->name }}</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-person"></i> Nombre</label>
                        <input type="text" name="name" class="form-control-custom" value="{{ $usuario->name }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-envelope"></i> Email</label>
                        <input type="email" name="email" class="form-control-custom" value="{{ $usuario->email }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-key"></i> Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control-custom" placeholder="Dejar vacío para mantener la actual">
                        <small class="text-muted">Solo complete si desea cambiar la contraseña</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-shield"></i> Rol</label>
                        <select name="rol" class="form-select-custom" required>
                            <option value="mesero" {{ $usuario->rol === 'mesero' ? 'selected' : '' }}>Mesero</option>
                            <option value="cocina" {{ $usuario->rol === 'cocina' ? 'selected' : '' }}>Cocina</option>
                            <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-success-custom">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary-custom">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
