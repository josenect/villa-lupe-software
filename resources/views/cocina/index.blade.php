@extends('layouts.app')

@section('title', 'Cocina - Villa Lupe')

@section('styles')
<style>
    .pedido-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #f39c12;
        transition: all 0.3s ease;
    }
    .pedido-card.en-cocina {
        border-left-color: #e74c3c;
        background: #fff5f5;
    }
    .pedido-card.listo {
        border-left-color: #27ae60;
        background: #f0fff4;
    }
    .pedido-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .mesa-badge {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .observacion-text {
        background: #fff3cd;
        padding: 0.5rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }
    .btn-listo {
        background: linear-gradient(135deg, #27ae60, #1e8449);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-listo:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
    }
    .refresh-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db, #2980b9);
        border: none;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        z-index: 1000;
    }
    .contador-pedidos {
        font-size: 3rem;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-egg-fried"></i> Cocina
    </h1>
    <a href="{{ route('cocina.index') }}" class="btn-primary-custom">
        <i class="bi bi-arrow-clockwise"></i> Actualizar
    </a>
</div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Resumen -->
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-4">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-clock text-warning" style="font-size: 2.5rem;"></i>
                <h2 class="contador-pedidos text-warning mt-2 mb-0">{{ $pedidos->where('estado', 'pendiente')->count() }}</h2>
                <p class="text-muted mb-0">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-fire text-danger" style="font-size: 2.5rem;"></i>
                <h2 class="contador-pedidos text-danger mt-2 mb-0">{{ $pedidos->where('estado', 'en_cocina')->count() }}</h2>
                <p class="text-muted mb-0">En Cocina</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                <h2 class="contador-pedidos text-success mt-2 mb-0">{{ $pedidosListos->count() }}</h2>
                <p class="text-muted mb-0">Listos (últimos)</p>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Pendientes -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #e67e22, #d35400) !important;">
        <h2><i class="bi bi-list-task"></i> Pedidos por Preparar ({{ $pedidos->count() }})</h2>
    </div>
    <div class="card-body-custom">
        @if($pedidos->count() > 0)
            <div class="row">
                @foreach ($pedidos as $pedido)
                    <div class="col-md-6 col-lg-4">
                        <div class="pedido-card {{ $pedido->estado === 'en_cocina' ? 'en-cocina' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="mesa-badge badge bg-primary">
                                    <i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}
                                </span>
                                <span class="badge {{ $pedido->estado === 'en_cocina' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $pedido->estado === 'en_cocina' ? 'En Cocina' : 'Pendiente' }}
                                </span>
                            </div>
                            
                            <h4 class="mb-1">
                                <span class="badge bg-secondary me-1">{{ $pedido->amount }}x</span>
                                {{ $pedido->producto->name }}
                                <small class="text-muted">${{ number_format($pedido->producto->price, 0, ',', '.') }}</small>
                            </h4>
                            
                            @if($pedido->observacion)
                                <div class="observacion-text">
                                    <i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> {{ date('H:i', strtotime($pedido->record)) }}
                                </small>
                                <div class="d-flex gap-2">
                                    @if($pedido->estado === 'pendiente')
                                        <form action="{{ route('cocina.enCocina', $pedido->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-fire"></i> En Cocina
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('cocina.listo', $pedido->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-listo">
                                            <i class="bi bi-check-lg"></i> Listo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-emoji-smile text-success" style="font-size: 4rem;"></i>
                <h3 class="text-success mt-3">¡Todo al día!</h3>
                <p class="text-muted">No hay pedidos pendientes</p>
            </div>
        @endif
    </div>
</div>

<!-- Pedidos Listos (últimos) -->
@if($pedidosListos->count() > 0)
<div class="card-custom fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #27ae60, #1e8449) !important;">
        <h2><i class="bi bi-check-circle"></i> Últimos Listos</h2>
    </div>
    <div class="card-body-custom">
        <div class="row">
            @foreach ($pedidosListos as $pedido)
                <div class="col-md-6 col-lg-4">
                    <div class="pedido-card listo">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ $pedido->mesa->name ?? 'Mesa' }}</span>
                            <span class="badge bg-success"><i class="bi bi-check"></i> Listo</span>
                        </div>
                        <h5 class="mb-0 mt-2">
                            <span class="badge bg-secondary">{{ $pedido->amount }}x</span>
                            {{ $pedido->producto->name }}
                            <small class="text-muted">${{ number_format($pedido->producto->price, 0, ',', '.') }}</small>
                        </h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Botón flotante de actualizar -->
<button onclick="location.reload()" class="refresh-btn" title="Actualizar">
    <i class="bi bi-arrow-clockwise"></i>
</button>
@endsection

@section('scripts')
<script>
    // Auto-refresh cada 30 segundos
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endsection
