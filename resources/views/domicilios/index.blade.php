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
        <a href="{{ route('domicilios.create') }}" class="btn-success-custom">
            <i class="bi bi-plus-circle"></i> Nuevo Domicilio
        </a>
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
                                <h4 class="mb-0"><i class="bi bi-truck"></i> {{ $dom->name }}</h4>
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

                            @if($dom->occupied_at)
                                <div class="mesa-info mt-1">
                                    <i class="bi bi-clock-history"></i>
                                    <span class="tiempo-mesa" data-since="{{ $dom->occupied_at }}">--</span>
                                </div>
                            @endif

                            <div class="mt-3 d-flex gap-2">
                                <a href="{{ route('mesa.show', $dom->id) }}" class="btn-primary-custom flex-fill justify-content-center">
                                    <i class="bi bi-pencil-square"></i> Gestionar
                                </a>
                                <a href="{{ route('domicilios.edit', $dom->id) }}" class="btn-warning-custom justify-content-center" title="Editar datos cliente">
                                    <i class="bi bi-person-gear"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
