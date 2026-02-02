@extends('layouts.app')

@section('title', 'Mis Pedidos - Villa Lupe')

@section('styles')
<style>
    .pedido-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .pedido-card.listo {
        border-left: 4px solid #27ae60;
        background: linear-gradient(135deg, #f0fff4, white);
    }
    .pedido-card.pendiente {
        border-left: 4px solid #f39c12;
    }
    .pedido-card.en-cocina {
        border-left: 4px solid #e74c3c;
        background: #fff5f5;
    }
    .btn-entregar {
        background: linear-gradient(135deg, #27ae60, #1e8449);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-entregar:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
        color: white;
    }
    .contador-badge {
        font-size: 2rem;
        font-weight: 700;
    }
    .observacion-text {
        background: #fff3cd;
        padding: 0.3rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }
    .section-cocina {
        border-left: 4px solid #e74c3c;
    }
    .section-otros {
        border-left: 4px solid #3498db;
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #666;
    }
    .nav-tabs .nav-link.active {
        color: #2c3e50;
    }
    .tab-badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .contador-badge {
            font-size: 1.5rem;
        }
        .pedido-card {
            padding: 0.75rem;
        }
        .nav-tabs .nav-link {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-clipboard-check"></i> Mis Pedidos
    </h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn-primary-custom" id="btn-actualizar" onclick="actualizarManual()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <a href="/" class="btn-secondary-custom">
            <i class="bi bi-house"></i>
        </a>
    </div>
</div>

<!-- Resumen General -->
<div class="row g-2 mb-4 fade-in">
    <div class="col-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-bell-fill text-success" style="font-size: 1.5rem;"></i>
                <h3 class="contador-badge text-success mb-0" id="total-listos">{{ $pedidosListosCocina->count() }}</h3>
                <p class="text-muted mb-0 small">Listos</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-egg-fried text-danger" style="font-size: 1.5rem;"></i>
                <h3 class="contador-badge text-danger mb-0" id="total-cocina">{{ $pedidosListosCocina->count() + $pedidosEnProcesoCocina->count() }}</h3>
                <p class="text-muted mb-0 small">Cocina</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-shop text-info" style="font-size: 1.5rem;"></i>
                <h3 class="contador-badge text-info mb-0" id="total-otros">{{ $pedidosListosOtros->count() + $pedidosEnProcesoOtros->count() }}</h3>
                <p class="text-muted mb-0 small">Otros</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-hourglass-split text-warning" style="font-size: 1.5rem;"></i>
                <h3 class="contador-badge text-warning mb-0" id="total-proceso">{{ $pedidosEnProcesoCocina->count() }}</h3>
                <p class="text-muted mb-0 small">En Cocina</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs para Cocina y Otros -->
<ul class="nav nav-tabs mb-3 fade-in" id="pedidosTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="cocina-tab" data-bs-toggle="tab" data-bs-target="#cocina" type="button">
            <i class="bi bi-egg-fried text-danger"></i> Cocina 
            <span class="badge bg-danger tab-badge" id="badge-cocina">{{ $pedidosListosCocina->count() + $pedidosEnProcesoCocina->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="otros-tab" data-bs-toggle="tab" data-bs-target="#otros" type="button">
            <i class="bi bi-shop text-info"></i> Otros 
            <span class="badge bg-info tab-badge" id="badge-otros">{{ $pedidosListosOtros->count() + $pedidosEnProcesoOtros->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="pedidosTabsContent">
    <!-- TAB COCINA -->
    <div class="tab-pane fade show active" id="cocina" role="tabpanel">
        <!-- Listos Cocina - SIEMPRE VISIBLE -->
        <div class="card-custom mb-3 section-cocina fade-in">
            <div class="card-header-custom py-2" style="background: linear-gradient(135deg, #27ae60, #1e8449) !important;">
                <h5 class="mb-0"><i class="bi bi-bell-fill"></i> ¡Listos para Llevar! (<span id="listos-cocina-count">{{ $pedidosListosCocina->count() }}</span>)</h5>
            </div>
            <div class="card-body-custom py-2" id="listos-cocina-container">
                @if($pedidosListosCocina->count() > 0)
                    <div class="row g-2">
                        @foreach ($pedidosListosCocina as $pedido)
                            <div class="col-12 col-md-6 col-lg-4" id="pedido-{{ $pedido->id }}">
                                <div class="pedido-card listo">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary"><i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                        <small class="text-muted"><i class="bi bi-clock"></i> {{ $pedido->updated_at->format('H:i') }}</small>
                                    </div>
                                    <h6 class="mb-1">
                                        <span class="badge bg-success">{{ $pedido->amount }}x</span>
                                        {{ $pedido->producto->name }}
                                    </h6>
                                    @if($pedido->observacion)
                                        <div class="observacion-text"><i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}</div>
                                    @endif
                                    <button type="button" class="btn-entregar mt-2" onclick="marcarEntregado({{ $pedido->id }})">
                                        <i class="bi bi-check2-circle"></i> Entregado
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-hourglass-split text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">No hay pedidos listos</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- En Proceso Cocina -->
        <div class="card-custom section-cocina fade-in">
            <div class="card-header-custom py-2" style="background: linear-gradient(135deg, #e67e22, #d35400) !important;">
                <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> En Proceso (<span id="proceso-cocina-count">{{ $pedidosEnProcesoCocina->count() }}</span>)</h5>
            </div>
            <div class="card-body-custom py-2" id="proceso-cocina-container">
                @if($pedidosEnProcesoCocina->count() > 0)
                    <div class="row g-2">
                        @foreach ($pedidosEnProcesoCocina as $pedido)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pedido-card {{ $pedido->estado === 'en_cocina' ? 'en-cocina' : 'pendiente' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary"><i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                        <span class="badge {{ $pedido->estado === 'en_cocina' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                            {{ $pedido->estado === 'en_cocina' ? 'En Cocina' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary">{{ $pedido->amount }}x</span>
                                        {{ $pedido->producto->name }}
                                    </h6>
                                    @if($pedido->observacion)
                                        <small class="text-info"><i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">Sin pedidos en proceso</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Entregados Hoy - Cocina -->
        <div class="card-custom section-cocina fade-in mt-3">
            <div class="card-header-custom py-2" style="background: linear-gradient(135deg, #6c757d, #495057) !important;">
                <h5 class="mb-0"><i class="bi bi-check-circle-fill"></i> Entregados Hoy (<span id="entregados-cocina-count">{{ $pedidosEntregadosCocina->count() }}</span>)</h5>
            </div>
            <div class="card-body-custom py-2" id="entregados-cocina-container">
                @if($pedidosEntregadosCocina->count() > 0)
                    <div class="row g-2">
                        @foreach ($pedidosEntregadosCocina as $pedido)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pedido-card listo" style="opacity: {{ $pedido->status == 0 ? '0.6' : '0.9' }};">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary"><i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                        <span class="badge {{ $pedido->status == 0 ? 'bg-secondary' : 'bg-success' }}">
                                            <i class="bi bi-check"></i> {{ $pedido->updated_at->format('H:i') }}
                                            @if($pedido->status == 0) <i class="bi bi-receipt ms-1"></i> @endif
                                        </span>
                                    </div>
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary">{{ $pedido->amount }}x</span>
                                        {{ $pedido->producto->name }}
                                        @if($pedido->status == 0)
                                            <small class="text-muted">(Facturado)</small>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">No hay entregas aún</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB OTROS (Caseta, etc) - No necesitan preparación -->
    <div class="tab-pane fade" id="otros" role="tabpanel">
        <!-- Pendientes de entregar -->
        <div class="card-custom section-otros fade-in mb-3">
            <div class="card-header-custom py-2" style="background: linear-gradient(135deg, #3498db, #2980b9) !important;">
                <h5 class="mb-0"><i class="bi bi-shop"></i> Pendientes de Entregar (<span id="proceso-otros-count">{{ $pedidosEnProcesoOtros->count() + $pedidosListosOtros->count() }}</span>)</h5>
            </div>
            <div class="card-body-custom py-2" id="proceso-otros-container">
                @php
                    $todosOtros = $pedidosEnProcesoOtros->merge($pedidosListosOtros);
                @endphp
                @if($todosOtros->count() > 0)
                    <div class="row g-2">
                        @foreach ($todosOtros as $pedido)
                            <div class="col-12 col-md-6 col-lg-4" id="pedido-{{ $pedido->id }}">
                                <div class="pedido-card pendiente">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary"><i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                        <small class="text-muted"><i class="bi bi-clock"></i> {{ date('H:i', strtotime($pedido->record)) }}</small>
                                    </div>
                                    <h6 class="mb-1">
                                        <span class="badge bg-info">{{ $pedido->amount }}x</span>
                                        {{ $pedido->producto->name }}
                                    </h6>
                                    @if($pedido->observacion)
                                        <div class="observacion-text"><i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}</div>
                                    @endif
                                    <button type="button" class="btn-entregar mt-2" onclick="marcarEntregado({{ $pedido->id }})">
                                        <i class="bi bi-check2-circle"></i> Entregado
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">Sin pedidos pendientes</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Entregados Hoy -->
        <div class="card-custom section-otros fade-in">
            <div class="card-header-custom py-2" style="background: linear-gradient(135deg, #27ae60, #1e8449) !important;">
                <h5 class="mb-0"><i class="bi bi-check-circle-fill"></i> Entregados Hoy (<span id="entregados-otros-count">{{ $pedidosEntregadosOtros->count() }}</span>)</h5>
            </div>
            <div class="card-body-custom py-2" id="entregados-otros-container">
                @if($pedidosEntregadosOtros->count() > 0)
                    <div class="row g-2">
                        @foreach ($pedidosEntregadosOtros as $pedido)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pedido-card listo" style="opacity: {{ $pedido->status == 0 ? '0.6' : '0.9' }};">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary"><i class="bi bi-table"></i> {{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                        <span class="badge {{ $pedido->status == 0 ? 'bg-secondary' : 'bg-success' }}">
                                            <i class="bi bi-check"></i> {{ $pedido->updated_at->format('H:i') }}
                                            @if($pedido->status == 0) <i class="bi bi-receipt ms-1"></i> @endif
                                        </span>
                                    </div>
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary">{{ $pedido->amount }}x</span>
                                        {{ $pedido->producto->name }}
                                        @if($pedido->status == 0)
                                            <small class="text-muted">(Facturado)</small>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">No hay entregas aún</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const REFRESH_TIME = {{ $refreshTime }};
    let refreshInterval;
    let pedidosListosAnteriores = {
        cocina: [{{ $pedidosListosCocina->pluck('id')->implode(',') }}],
        otros: [{{ $pedidosListosOtros->pluck('id')->implode(',') }}]
    };

    // Audio
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioContext = null;

    function inicializarAudio() {
        if (!audioContext) {
            audioContext = new AudioContext();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }

    function reproducirSonido() {
        if (!audioContext) return;
        try {
            [0, 150, 300].forEach((delay, i) => {
                setTimeout(() => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.frequency.value = [523, 659, 784][i];
                    gain.gain.setValueAtTime(0.2, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                    osc.start();
                    osc.stop(audioContext.currentTime + 0.2);
                }, delay);
            });
        } catch (e) {}
    }

    function iniciarAutoRefresh() {
        refreshInterval = setInterval(cargarPedidos, REFRESH_TIME);
    }

    function cargarPedidos() {
        fetch('{{ route("mesero.pedidos.ajax") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            verificarNuevosListos(data);
            actualizarContadores(data.contadores);
            actualizarSeccion('cocina', data.cocina);
            actualizarSeccion('otros', data.otros);
        })
        .catch(error => console.error('Error:', error));
    }

    function verificarNuevosListos(data) {
        const nuevosListosCocina = data.cocina.listos.map(p => p.id).filter(id => !pedidosListosAnteriores.cocina.includes(id));
        const nuevosListosOtros = data.otros.listos.map(p => p.id).filter(id => !pedidosListosAnteriores.otros.includes(id));
        
        if (nuevosListosCocina.length > 0 || nuevosListosOtros.length > 0) {
            reproducirSonido();
            const total = nuevosListosCocina.length + nuevosListosOtros.length;
            mostrarNotificacion('success', `¡${total} pedido(s) listo(s)!`);
        }
        
        pedidosListosAnteriores.cocina = data.cocina.listos.map(p => p.id);
        pedidosListosAnteriores.otros = data.otros.listos.map(p => p.id);
    }

    function actualizarContadores(c) {
        const totalOtros = c.listos_otros + c.proceso_otros;
        document.getElementById('total-listos').textContent = c.listos_cocina; // Solo listos de cocina
        document.getElementById('total-cocina').textContent = c.listos_cocina + c.proceso_cocina;
        document.getElementById('total-otros').textContent = totalOtros;
        document.getElementById('total-proceso').textContent = c.proceso_cocina; // Solo proceso de cocina
        document.getElementById('badge-cocina').textContent = c.listos_cocina + c.proceso_cocina;
        document.getElementById('badge-otros').textContent = totalOtros;
    }

    function generarPedidoListoHTML(pedido) {
        let obs = pedido.observacion ? `<div class="observacion-text"><i class="bi bi-chat-dots"></i> ${pedido.observacion}</div>` : '';
        return `
            <div class="col-12 col-md-6 col-lg-4" id="pedido-${pedido.id}">
                <div class="pedido-card listo">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary"><i class="bi bi-table"></i> ${pedido.mesa_nombre}</span>
                        <small class="text-muted"><i class="bi bi-clock"></i> ${pedido.updated_at}</small>
                    </div>
                    <h6 class="mb-1"><span class="badge bg-success">${pedido.amount}x</span> ${pedido.producto_nombre}</h6>
                    ${obs}
                    <button type="button" class="btn-entregar mt-2" onclick="marcarEntregado(${pedido.id})">
                        <i class="bi bi-check2-circle"></i> Entregado
                    </button>
                </div>
            </div>
        `;
    }

    function generarPedidoProcesoHTML(pedido) {
        const esEnCocina = pedido.estado === 'en_cocina';
        const cardClass = esEnCocina ? 'pedido-card en-cocina' : 'pedido-card pendiente';
        const badgeClass = esEnCocina ? 'bg-danger' : 'bg-warning text-dark';
        const estadoText = esEnCocina ? 'En Cocina' : 'Pendiente';
        let obs = pedido.observacion ? `<small class="text-info"><i class="bi bi-chat-dots"></i> ${pedido.observacion}</small>` : '';
        
        return `
            <div class="col-12 col-md-6 col-lg-4">
                <div class="${cardClass}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary"><i class="bi bi-table"></i> ${pedido.mesa_nombre}</span>
                        <span class="badge ${badgeClass}">${estadoText}</span>
                    </div>
                    <h6 class="mb-0"><span class="badge bg-secondary">${pedido.amount}x</span> ${pedido.producto_nombre}</h6>
                    ${obs}
                </div>
            </div>
        `;
    }

    function actualizarSeccion(tipo, data) {
        if (tipo === 'cocina') {
            // COCINA: tiene sección de listos, en proceso y entregados
            const listosContainer = document.getElementById('listos-cocina-container');
            const procesoContainer = document.getElementById('proceso-cocina-container');
            const entregadosContainer = document.getElementById('entregados-cocina-container');
            const listosCount = document.getElementById('listos-cocina-count');
            const procesoCount = document.getElementById('proceso-cocina-count');
            const entregadosCount = document.getElementById('entregados-cocina-count');
            
            // Actualizar listos
            if (listosContainer) {
                if (data.listos.length > 0) {
                    let html = '<div class="row g-2">';
                    data.listos.forEach(p => html += generarPedidoListoHTML(p));
                    html += '</div>';
                    listosContainer.innerHTML = html;
                } else {
                    listosContainer.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-hourglass-split text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No hay pedidos listos</p>
                        </div>
                    `;
                }
                if (listosCount) listosCount.textContent = data.listos.length;
            }
            
            // Actualizar en proceso
            if (procesoContainer) {
                if (data.enProceso.length > 0) {
                    let html = '<div class="row g-2">';
                    data.enProceso.forEach(p => html += generarPedidoProcesoHTML(p));
                    html += '</div>';
                    procesoContainer.innerHTML = html;
                } else {
                    procesoContainer.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Sin pedidos en proceso</p>
                        </div>
                    `;
                }
                if (procesoCount) procesoCount.textContent = data.enProceso.length;
            }
            
            // Actualizar entregados cocina
            if (entregadosContainer && data.entregados) {
                if (data.entregados.length > 0) {
                    let html = '<div class="row g-2">';
                    data.entregados.forEach(p => html += generarPedidoEntregadoHTML(p));
                    html += '</div>';
                    entregadosContainer.innerHTML = html;
                } else {
                    entregadosContainer.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No hay entregas aún</p>
                        </div>
                    `;
                }
                if (entregadosCount) entregadosCount.textContent = data.entregados.length;
            }
        } else {
            // OTROS: pedidos pendientes con botón entregado + historial de entregados
            const container = document.getElementById('proceso-otros-container');
            const count = document.getElementById('proceso-otros-count');
            const entregadosContainer = document.getElementById('entregados-otros-container');
            const entregadosCount = document.getElementById('entregados-otros-count');
            
            // Combinar listos y en proceso (pendientes de entregar)
            const todosPedidos = [...data.listos, ...data.enProceso];
            
            if (container) {
                if (todosPedidos.length > 0) {
                    let html = '<div class="row g-2">';
                    todosPedidos.forEach(p => html += generarPedidoOtrosHTML(p));
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Sin pedidos pendientes</p>
                        </div>
                    `;
                }
                if (count) count.textContent = todosPedidos.length;
            }
            
            // Actualizar entregados
            if (entregadosContainer && data.entregados) {
                if (data.entregados.length > 0) {
                    let html = '<div class="row g-2">';
                    data.entregados.forEach(p => html += generarPedidoEntregadoHTML(p));
                    html += '</div>';
                    entregadosContainer.innerHTML = html;
                } else {
                    entregadosContainer.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No hay entregas aún</p>
                        </div>
                    `;
                }
                if (entregadosCount) entregadosCount.textContent = data.entregados.length;
            }
        }
    }
    
    // HTML para pedidos entregados
    function generarPedidoEntregadoHTML(pedido) {
        const opacity = pedido.facturado ? '0.6' : '0.9';
        const badgeClass = pedido.facturado ? 'bg-secondary' : 'bg-success';
        const receiptIcon = pedido.facturado ? '<i class="bi bi-receipt ms-1"></i>' : '';
        const facturadoText = pedido.facturado ? '<small class="text-muted">(Facturado)</small>' : '';
        
        return `
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pedido-card listo" style="opacity: ${opacity};">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary"><i class="bi bi-table"></i> ${pedido.mesa_nombre}</span>
                        <span class="badge ${badgeClass}"><i class="bi bi-check"></i> ${pedido.updated_at}${receiptIcon}</span>
                    </div>
                    <h6 class="mb-0">
                        <span class="badge bg-secondary">${pedido.amount}x</span>
                        ${pedido.producto_nombre}
                        ${facturadoText}
                    </h6>
                </div>
            </div>
        `;
    }
    
    // HTML para pedidos de Otros (con botón entregado directo)
    function generarPedidoOtrosHTML(pedido) {
        let obs = pedido.observacion ? `<div class="observacion-text"><i class="bi bi-chat-dots"></i> ${pedido.observacion}</div>` : '';
        const hora = pedido.updated_at || pedido.record;
        return `
            <div class="col-12 col-md-6 col-lg-4" id="pedido-${pedido.id}">
                <div class="pedido-card pendiente">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary"><i class="bi bi-table"></i> ${pedido.mesa_nombre}</span>
                        <small class="text-muted"><i class="bi bi-clock"></i> ${hora}</small>
                    </div>
                    <h6 class="mb-1"><span class="badge bg-info">${pedido.amount}x</span> ${pedido.producto_nombre}</h6>
                    ${obs}
                    <button type="button" class="btn-entregar mt-2" onclick="marcarEntregado(${pedido.id})">
                        <i class="bi bi-check2-circle"></i> Entregado
                    </button>
                </div>
            </div>
        `;
    }

    function marcarEntregado(id) {
        const card = document.getElementById('pedido-' + id);
        if (!card) return;
        const btn = card.querySelector('.btn-entregar');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch(`/mesero/pedidos/${id}/entregado`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => cargarPedidos(), 300);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle"></i> Entregado';
        });
    }

    function mostrarNotificacion(tipo, mensaje) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-custom alert-success-custom fade-in`;
        alertDiv.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;min-width:200px;';
        alertDiv.innerHTML = `<i class="bi bi-bell-fill fs-5"></i><span>${mensaje}</span>`;
        document.body.appendChild(alertDiv);
        setTimeout(() => { alertDiv.style.opacity = '0'; setTimeout(() => alertDiv.remove(), 300); }, 3000);
    }

    function actualizarManual() {
        const btn = document.getElementById('btn-actualizar');
        const icon = btn.querySelector('i');
        icon.style.animation = 'spin 1s linear';
        btn.disabled = true;
        cargarPedidos();
        setTimeout(() => { icon.style.animation = ''; btn.disabled = false; }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        iniciarAutoRefresh();
        document.body.addEventListener('click', () => inicializarAudio(), { once: true });
    });
</script>
<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
