@extends('layouts.app')

@section('title', 'Domicilios')

@section('content')

<div class="text-center mb-4 fade-in">
    <h1 class="page-title"><i class="bi bi-truck"></i> Domicilios</h1>
    <p class="text-white opacity-75">Pedidos a domicilio activos</p>
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

<div class="card-custom fade-in">
    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-truck"></i> Pedidos Activos</h2>
        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->user()->esAdmin())
                <a href="{{ route('domicilios.historial') }}" class="btn-secondary-custom">
                    <i class="bi bi-clock-history"></i> Historial
                </a>
            @endif
            <a href="{{ route('domicilios.create') }}" class="btn-success-custom">
                <i class="bi bi-plus-circle"></i> Nuevo Domicilio
            </a>
        </div>
    </div>
    <div class="card-body-custom">
        @if($domicilios->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-truck" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 text-muted">No hay domicilios activos</p>
                <a href="{{ route('domicilios.create') }}" class="btn-primary-custom mt-2">
                    <i class="bi bi-plus-circle"></i> Crear Domicilio
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($domicilios as $dom)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="mesa-card ocupada">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="mb-0"><i class="bi bi-truck"></i> {{ $dom->mesa->name }}</h4>
                                <span class="status-badge ocupada">Activo</span>
                            </div>

                            <div class="mesa-info mb-1">
                                <i class="bi bi-person-fill"></i> {{ $dom->cliente_nombre }}
                            </div>
                            <div class="mesa-info mb-1">
                                <i class="bi bi-telephone-fill"></i> {{ $dom->cliente_telefono }}
                            </div>
                            <div class="mesa-info mb-1">
                                <i class="bi bi-geo-alt-fill"></i> {{ $dom->cliente_direccion }}
                            </div>

                            @if($dom->total_productos > 0)
                                <div class="mesa-info mt-2">
                                    <i class="bi bi-basket-fill"></i>
                                    {{ $dom->total_productos }} producto(s) &mdash;
                                    <strong>${{ number_format($dom->subtotal, 0, ',', '.') }}</strong>
                                </div>
                            @else
                                <div class="mesa-info mt-2 text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Sin productos
                                </div>
                            @endif

                            @if($dom->mesa->occupied_at)
                                <div class="mesa-info mt-1">
                                    <i class="bi bi-clock-history"></i>
                                    <span class="tiempo-mesa" data-since="{{ $dom->mesa->occupied_at }}">--</span>
                                </div>
                            @endif

                            <div class="mt-3 d-flex flex-column gap-2">
                                <a href="{{ route('mesa.show', $dom->table_id) }}" class="btn-primary-custom w-100 justify-content-center" style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">
                                    <i class="bi bi-pencil-square"></i> Gestionar Pedido
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('domicilios.edit', $dom->id) }}" class="btn-warning-custom flex-fill justify-content-center" style="padding: 0.4rem 0.5rem; font-size: 0.8rem;">
                                        <i class="bi bi-person-gear"></i> Editar
                                    </a>
                                    @if(auth()->user()->esAdmin())
                                        <button type="button" class="btn-danger-custom flex-fill justify-content-center" style="padding: 0.4rem 0.5rem; font-size: 0.8rem;"
                                                onclick="confirmarCancelar({{ $dom->id }}, '{{ $dom->cliente_nombre }}')">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->esAdmin())
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Cancelar Domicilio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-x-circle" style="font-size: 3rem; color: #e74c3c;"></i>
                <p class="mt-3 mb-0">
                    Se cancelara el domicilio de <strong id="cancelNombre"></strong> y todos sus productos.
                    <br><small class="text-muted">Esta accion no se puede deshacer.</small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">No, volver</button>
                <form id="formCancelar" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger-custom">
                        <i class="bi bi-x-circle"></i> Si, cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@if(auth()->user()->esAdmin())
@section('scripts')
<script>
function confirmarCancelar(id, nombre) {
    document.getElementById('cancelNombre').textContent = nombre;
    document.getElementById('formCancelar').action = '/domicilios/' + id + '/cancelar';
    new bootstrap.Modal(document.getElementById('modalCancelar')).show();
}
</script>
@endsection
@endif
