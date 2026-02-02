@extends('layouts.app')

@section('title', 'Pedidos por Mesero - Admin')

@section('styles')
<style>
    .mesero-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 4px solid #6c757d;
    }
    .mesero-card:hover, .mesero-card.active {
        border-left-color: #3498db;
        background: #f8f9fa;
    }
    .mesero-card.active {
        border-left-color: #27ae60;
        background: #f0fff4;
    }
    .pedido-item {
        background: white;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .pedido-item.pendiente { border-left: 3px solid #f39c12; }
    .pedido-item.en_cocina { border-left: 3px solid #e74c3c; }
    .pedido-item.listo { border-left: 3px solid #27ae60; }
    .pedido-item.entregado { border-left: 3px solid #3498db; }
    .pedido-item.facturado { border-left: 3px solid #6c757d; opacity: 0.7; }
    
    .stats-badge {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .section-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }
    .section-body {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0 0 8px 8px;
        max-height: 400px;
        overflow-y: auto;
    }
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .stats-badge { font-size: 1.2rem; }
        .section-body { max-height: 300px; }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-people"></i> Pedidos por Mesero
    </h1>
    <a href="/" class="btn-secondary-custom">
        <i class="bi bi-house"></i>
    </a>
</div>

<!-- Filtro de Mesero -->
<div class="filter-section fade-in">
    <form method="GET" action="{{ route('admin.pedidos.meseros') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label mb-1"><i class="bi bi-person"></i> Seleccionar Mesero</label>
            <select name="mesero_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Todos los Meseros --</option>
                @foreach($meseros as $mesero)
                    <option value="{{ $mesero->id }}" {{ $meseroSeleccionado == $mesero->id ? 'selected' : '' }}>
                        {{ $mesero->name }} ({{ $mesero->rol }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('admin.pedidos.meseros') }}" class="btn-secondary-custom">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Resumen Rápido de Meseros -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Resumen de Meseros (Últimas 24h)</h5>
    </div>
    <div class="card-body-custom">
        <div class="row g-2" id="resumen-meseros">
            @foreach($meseros as $mesero)
                @php
                    $pendientes = $pedidosPendientes->where('user_id', $mesero->id)->count();
                    $entregados = $pedidosEntregados->where('user_id', $mesero->id)->count();
                    if (!$meseroSeleccionado) {
                        $pendientes = \App\Models\ElementTable::where('user_id', $mesero->id)
                            ->where('status', 1)
                            ->whereIn('estado', ['pendiente', 'en_cocina', 'listo'])
                            ->count();
                        $entregados = \App\Models\ElementTable::where('user_id', $mesero->id)
                            ->where('updated_at', '>=', now()->subHours(24))
                            ->where(function($q) {
                                $q->where(function($sub) {
                                    $sub->where('status', 1)->where('estado', 'entregado');
                                })->orWhere('status', 0);
                            })
                            ->count();
                    }
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('admin.pedidos.meseros', ['mesero_id' => $mesero->id]) }}" class="text-decoration-none">
                        <div class="mesero-card {{ $meseroSeleccionado == $mesero->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $mesero->name }}</strong>
                                    <br><small class="text-muted">{{ $mesero->rol }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark">{{ $pendientes }}</span>
                                    <span class="badge bg-success">{{ $entregados }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-2">
            <small class="text-muted">
                <span class="badge bg-warning text-dark">N</span> = Pendientes de entregar | 
                <span class="badge bg-success">N</span> = Entregados
            </small>
        </div>
    </div>
</div>

<!-- Detalle del Mesero Seleccionado -->
<div class="row g-3">
    <!-- Pendientes de Entregar -->
    <div class="col-12 col-lg-6 fade-in">
        <div class="section-header" style="background: linear-gradient(135deg, #e67e22, #d35400) !important;">
            <h5 class="mb-0">
                <i class="bi bi-hourglass-split"></i> Pendientes de Entregar 
                <span class="badge bg-light text-dark">{{ $pedidosPendientes->count() }}</span>
                @if($meseroSeleccionado)
                    <small class="ms-2">- {{ $meseroNombre }}</small>
                @endif
            </h5>
        </div>
        <div class="section-body">
            @if($pedidosPendientes->count() > 0)
                @foreach($pedidosPendientes as $pedido)
                    <div class="pedido-item {{ $pedido->estado }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $pedido->producto->name }}</strong>
                                <span class="badge bg-secondary">x{{ $pedido->amount }}</span>
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}
                                </small>
                                @if(!$meseroSeleccionado)
                                    <br><small class="text-info"><i class="bi bi-person"></i> {{ $pedido->usuario->name ?? 'N/A' }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                @switch($pedido->estado)
                                    @case('pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                        @break
                                    @case('en_cocina')
                                        <span class="badge bg-danger">En Cocina</span>
                                        @break
                                    @case('listo')
                                        <span class="badge bg-success">Listo</span>
                                        @break
                                @endswitch
                                <br><small class="text-muted">{{ date('H:i', strtotime($pedido->record)) }}</small>
                            </div>
                        </div>
                        @if($pedido->observacion)
                            <small class="text-warning"><i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}</small>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Sin pedidos pendientes</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Entregados -->
    <div class="col-12 col-lg-6 fade-in">
        <div class="section-header" style="background: linear-gradient(135deg, #27ae60, #1e8449) !important;">
            <h5 class="mb-0">
                <i class="bi bi-check-circle"></i> Entregados (24h) 
                <span class="badge bg-light text-dark">{{ $pedidosEntregados->count() }}</span>
                @if($meseroSeleccionado)
                    <small class="ms-2">- {{ $meseroNombre }}</small>
                @endif
            </h5>
        </div>
        <div class="section-body">
            @if($pedidosEntregados->count() > 0)
                @foreach($pedidosEntregados as $pedido)
                    <div class="pedido-item {{ $pedido->status == 0 ? 'facturado' : 'entregado' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $pedido->producto->name }}</strong>
                                <span class="badge bg-secondary">x{{ $pedido->amount }}</span>
                                @if($pedido->status == 0)
                                    <span class="badge bg-info">Facturado</span>
                                @endif
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}
                                </small>
                                @if(!$meseroSeleccionado)
                                    <br><small class="text-info"><i class="bi bi-person"></i> {{ $pedido->usuario->name ?? 'N/A' }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success"><i class="bi bi-check"></i></span>
                                <br><small class="text-muted">{{ $pedido->updated_at->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Sin entregas registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
