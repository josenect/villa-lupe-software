@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-people"></i> Gestión de Usuarios
    </h1>
    <a href="/" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Inicio
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

<!-- Formulario Nuevo Usuario -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-person-plus"></i> Nuevo Usuario</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-3">
                    <label class="form-label-custom"><i class="bi bi-person"></i> Nombre</label>
                    <input type="text" name="name" class="form-control-custom" placeholder="Nombre completo" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label-custom"><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control-custom" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label-custom"><i class="bi bi-key"></i> Contraseña</label>
                    <input type="password" name="password" class="form-control-custom" placeholder="••••••" required>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label-custom"><i class="bi bi-shield"></i> Rol</label>
                    <select name="rol" class="form-select-custom" required>
                        <option value="mesero">Mesero</option>
                        <option value="cocina">Cocina</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-success-custom w-100 justify-content-center" style="min-height: 48px;">
                        <i class="bi bi-plus-lg"></i> Crear
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Usuarios -->
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-people-fill"></i> Usuarios Registrados</h2>
    </div>
    <div class="card-body-custom">
        @if($usuarios->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person"></i> Nombre</th>
                            <th><i class="bi bi-envelope"></i> Email</th>
                            <th><i class="bi bi-shield"></i> Rol</th>
                            <th><i class="bi bi-toggle-on"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr class="{{ !$usuario->activo ? 'table-secondary' : '' }}">
                                <td data-label="Nombre"><strong>{{ $usuario->name }}</strong></td>
                                <td data-label="Email">{{ $usuario->email }}</td>
                                <td data-label="Rol">
                                    @if($usuario->rol === 'admin')
                                        <span class="badge bg-danger"><i class="bi bi-shield-check"></i> Admin</span>
                                    @elseif($usuario->rol === 'mesero')
                                        <span class="badge bg-primary"><i class="bi bi-person-badge"></i> Mesero</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-egg-fried"></i> Cocina</span>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    @if($usuario->activo)
                                        <span class="status-badge activo"><i class="bi bi-check-circle"></i> Activo</span>
                                    @else
                                        <span class="status-badge inactivo"><i class="bi bi-x-circle"></i> Inactivo</span>
                                    @endif
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn-primary-custom btn-sm-custom" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.usuarios.toggle', $usuario->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="{{ $usuario->activo ? 'btn-warning-custom' : 'btn-success-custom' }} btn-sm-custom" title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="bi bi-{{ $usuario->activo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if(auth()->id() !== $usuario->id)
                                            <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger-custom btn-sm-custom" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay usuarios registrados</p>
            </div>
        @endif
    </div>
</div>
@endsection
