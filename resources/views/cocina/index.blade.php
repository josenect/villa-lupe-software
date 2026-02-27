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
    .btn-sound {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid #27ae60;
        background: white;
        color: #27ae60;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-sound:hover {
        background: #27ae60;
        color: white;
        transform: scale(1.1);
    }
    .btn-sound.muted {
        border-color: #e74c3c;
        color: #e74c3c;
    }
    .btn-sound.muted:hover {
        background: #e74c3c;
        color: white;
    }
    .alert-info-custom {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        border-left: 4px solid #17a2b8;
    }
    @keyframes pulse-alert {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    .nuevo-pedido-highlight {
        animation: pulse-alert 0.5s ease-in-out 3;
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.6) !important;
    }
    
    .mesa-grupo-header {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        border: 2px solid #dee2e6;
        transition: background 0.15s;
    }
    .mesa-grupo-header:hover { background: #e9ecef; }
    .mesa-grupo-body {
        border: 2px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
    }
    .mesa-grupo-body .pedido-item:last-child { border-bottom: none !important; }
    .pedido-item { background: white; }

    /* Responsive para cocina */
    @media (max-width: 768px) {
        .contador-pedidos {
            font-size: 2rem;
        }
        
        .pedido-card {
            padding: 0.75rem;
        }
        
        .pedido-card h4 {
            font-size: 1rem;
        }
        
        .mesa-badge {
            font-size: 0.9rem;
        }
        
        .btn-listo {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }
        
        .btn-sound {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .refresh-btn {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            bottom: 15px;
            right: 15px;
        }
        
        #banner-sonido {
            flex-direction: column;
            text-align: center;
        }
        
        #banner-sonido button {
            margin-top: 0.5rem;
            margin-left: 0 !important;
        }
    }
    
    @media (max-width: 480px) {
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.3rem !important;
        }
        
        .pedido-card .d-flex.gap-2 {
            flex-direction: row;
            gap: 0.5rem !important;
        }
        
        .btn-listo, 
        .btn.btn-sm.btn-outline-danger {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-egg-fried"></i> Cocina
    </h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn-sound" id="btn-sonido" onclick="toggleSonido()" title="Activar/Desactivar sonido">
            <i class="bi bi-volume-up-fill" id="icono-sonido"></i>
        </button>
        <button type="button" class="btn-primary-custom" id="btn-actualizar" onclick="actualizarManual()">
            <i class="bi bi-arrow-clockwise"></i> Actualizar
        </button>
    </div>
</div>

<!-- El audio se activa automáticamente con cualquier interacción -->
<div id="banner-sonido" style="display:none;"></div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Resumen -->
<div class="row g-3 mb-4 fade-in">
    <div class="col-6 col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                <h2 class="contador-pedidos text-warning mt-1 mb-0" id="contador-pendientes">{{ $pedidos->where('estado', 'pendiente')->count() }}</h2>
                <p class="text-muted mb-0 small">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-fire text-danger" style="font-size: 2rem;"></i>
                <h2 class="contador-pedidos text-danger mt-1 mb-0" id="contador-encocina">{{ $pedidos->where('estado', 'en_cocina')->count() }}</h2>
                <p class="text-muted mb-0 small">En Cocina</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                <h2 class="contador-pedidos text-success mt-1 mb-0" id="contador-listos">{{ $pedidosListos->count() }}</h2>
                <p class="text-muted mb-0 small">Listos</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center py-2">
                <i class="bi bi-check2-all text-secondary" style="font-size: 2rem;"></i>
                <h2 class="contador-pedidos text-secondary mt-1 mb-0" id="contador-entregados">{{ $pedidosEntregados->count() }}</h2>
                <p class="text-muted mb-0 small">Entregados</p>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos por Mesa -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #e67e22, #d35400) !important;">
        <h2><i class="bi bi-list-task"></i> Mesas con Pedidos (<span id="pedidos-count">{{ $pedidos->count() }}</span> items)</h2>
    </div>
    <div class="card-body-custom" id="pedidos-pendientes-container">
        @if($pedidos->count() > 0)
            @foreach ($pedidos->groupBy('table_id') as $mesaId => $itemsMesa)
                @php
                    $pendientes = $itemsMesa->where('estado', 'pendiente')->count();
                    $enCocina   = $itemsMesa->where('estado', 'en_cocina')->count();
                    $mesaNombre = $itemsMesa->first()->mesa->name ?? 'Mesa';
                @endphp
                <div class="mesa-grupo mb-3" id="mesa-grupo-{{ $mesaId }}">
                    <div class="mesa-grupo-header d-flex justify-content-between align-items-center"
                         onclick="toggleMesaGrupo({{ $mesaId }})" style="cursor:pointer; user-select:none;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary" style="font-size:1rem;"><i class="bi bi-table"></i> {{ $mesaNombre }}</span>
                            @if($pendientes > 0)
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> {{ $pendientes }} pendiente{{ $pendientes > 1 ? 's' : '' }}</span>
                            @endif
                            @if($enCocina > 0)
                                <span class="badge bg-danger"><i class="bi bi-fire"></i> {{ $enCocina }} en cocina</span>
                            @endif
                        </div>
                        <i class="bi bi-chevron-down" id="chevron-{{ $mesaId }}" style="font-size:1.1rem; transition:transform 0.2s; flex-shrink:0;"></i>
                    </div>
                    <div class="mesa-grupo-body" id="body-{{ $mesaId }}" style="display:none;">
                        @foreach ($itemsMesa as $pedido)
                            <div class="pedido-item d-flex justify-content-between align-items-center py-2 px-3 border-bottom" id="pedido-{{ $pedido->id }}">
                                <div>
                                    <span class="fw-bold">{{ $pedido->amount }}x {{ $pedido->producto->name }}</span>
                                    @if($pedido->observacion)
                                        <div class="observacion-text mt-1"><i class="bi bi-chat-dots"></i> {{ $pedido->observacion }}</div>
                                    @endif
                                    <div class="mt-1">
                                        <small class="text-muted"><i class="bi bi-clock"></i> {{ date('H:i', strtotime($pedido->record)) }}</small>
                                        <small class="text-muted ms-2"><i class="bi bi-person"></i> {{ $pedido->usuario->name ?? 'N/A' }}</small>
                                        <span class="badge {{ $pedido->estado === 'en_cocina' ? 'bg-danger' : 'bg-warning text-dark' }} ms-1">
                                            {{ $pedido->estado === 'en_cocina' ? 'En Cocina' : 'Pendiente' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0 ms-2">
                                    @if($pedido->estado === 'pendiente')
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="marcarEnCocina({{ $pedido->id }})">
                                            <i class="bi bi-fire"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn-listo" onclick="marcarListo({{ $pedido->id }})">
                                        <i class="bi bi-check-lg"></i> Listo
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
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
<div id="pedidos-listos-section">
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
                            <div>
                                <span class="badge bg-primary">{{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                <span class="badge bg-info ms-1"><i class="bi bi-person"></i> {{ $pedido->usuario->name ?? 'N/A' }}</span>
                            </div>
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
</div>

<!-- Pedidos Entregados Hoy -->
<div id="pedidos-entregados-section" class="mt-4">
@if($pedidosEntregados->count() > 0)
<div class="card-custom fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #6c757d, #495057) !important;">
        <h2><i class="bi bi-check2-all"></i> Entregados Hoy (<span id="entregados-count">{{ $pedidosEntregados->count() }}</span>)</h2>
    </div>
    <div class="card-body-custom" style="max-height: 300px; overflow-y: auto;">
        <div class="row" id="entregados-container">
            @foreach ($pedidosEntregados as $pedido)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="pedido-card" style="border-left-color: {{ $pedido->status == 0 ? '#6c757d' : '#3498db' }}; opacity: {{ $pedido->status == 0 ? '0.7' : '0.9' }}; padding: 0.75rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">{{ $pedido->mesa->name ?? 'Mesa' }}</span>
                                @if($pedido->status == 0)
                                    <span class="badge bg-secondary"><i class="bi bi-receipt"></i></span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $pedido->updated_at->format('H:i') }}</small>
                        </div>
                        <div class="mt-1">
                            <span class="badge bg-secondary">{{ $pedido->amount }}x</span>
                            <strong>{{ $pedido->producto->name }}</strong>
                        </div>
                        <small class="text-info"><i class="bi bi-person"></i> {{ $pedido->usuario->name ?? 'N/A' }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
</div>

<!-- Botón flotante de actualizar -->
<button onclick="location.reload()" class="refresh-btn" title="Actualizar">
    <i class="bi bi-arrow-clockwise"></i>
</button>
@endsection

@section('scripts')
<!-- Audio para alertas -->
<audio id="sonido-nuevo-pedido" preload="auto">
    <source src="data:audio/wav;base64,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" type="audio/wav">
</audio>
<audio id="sonido-alerta" preload="auto">
    <source src="data:audio/wav;base64,UklGRl9vT19teleQgAUS5rI1d8BipSPWMgV4ByFqItUxBXkHIWoi1TEFeQYhaCLUMQR6BiFlItQxBHoGIWQi0zAEegYhYiLTMAR7BiL//wAA" type="audio/wav">
</audio>

<script>
    // Inicializar con los IDs de pedidos actuales para no sonar en la primera carga
    let pedidosAnteriores = [{{ $pedidos->pluck('id')->implode(',') }}];
    let sonidoHabilitado = true;
    let primeraVez = true;

    // Crear contexto de audio para generar sonidos
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioContext = null;

    // Inicializar audio (requiere interacción del usuario)
    function inicializarAudio() {
        if (!audioContext) {
            audioContext = new AudioContext();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }

    // Reproducir sonido de alerta para nuevos pedidos
    function reproducirSonidoNuevoPedido() {
        if (!sonidoHabilitado || !audioContext) return;
        
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // Nota A5
            oscillator.frequency.setValueAtTime(1100, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(880, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
            
            // Reproducir 3 veces
            setTimeout(() => reproducirBeep(1100, 0.15), 600);
            setTimeout(() => reproducirBeep(880, 0.2), 900);
        } catch (e) {
            console.log('Error reproduciendo sonido:', e);
        }
    }

    // Reproducir un beep simple
    function reproducirBeep(frecuencia, duracion) {
        if (!sonidoHabilitado || !audioContext) return;
        
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(frecuencia, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duracion);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + duracion);
        } catch (e) {
            console.log('Error en beep:', e);
        }
    }

    // Verificar si hay nuevos pedidos
    function verificarNuevosPedidos(pedidosNuevos) {
        if (primeraVez) {
            primeraVez = false;
            pedidosAnteriores = pedidosNuevos.map(p => p.id);
            return;
        }

        const idsNuevos = pedidosNuevos.map(p => p.id);
        const pedidosAgregados = idsNuevos.filter(id => !pedidosAnteriores.includes(id));
        
        if (pedidosAgregados.length > 0) {
            reproducirSonidoNuevoPedido();
            mostrarNotificacion('info', `¡${pedidosAgregados.length} nuevo(s) pedido(s)!`);
            
            // Efecto visual en el título
            parpadearTitulo(pedidosAgregados.length);
        }
        
        pedidosAnteriores = idsNuevos;
    }

    // Parpadear título de la página
    let parpadeoInterval = null;
    function parpadearTitulo(cantidad) {
        const tituloOriginal = document.title;
        let visible = true;
        
        if (parpadeoInterval) clearInterval(parpadeoInterval);
        
        parpadeoInterval = setInterval(() => {
            document.title = visible ? `🔔 ¡${cantidad} NUEVO(S) PEDIDO(S)!` : tituloOriginal;
            visible = !visible;
        }, 500);
        
        // Detener después de 10 segundos
        setTimeout(() => {
            clearInterval(parpadeoInterval);
            document.title = tituloOriginal;
        }, 10000);
    }

    // Escuchar eventos SSE del layout global para refrescar la UI
    window.addEventListener('vl:cocina_update', function() {
        cargarPedidos();
    });

    // Cargar pedidos via AJAX
    function cargarPedidos() {
        fetch('{{ route("cocina.pedidos") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Verificar nuevos pedidos antes de actualizar
            verificarNuevosPedidos(data.pedidos);
            
            actualizarContadores(data.contadores);
            actualizarPedidosPendientes(data.pedidos);
            actualizarPedidosListos(data.pedidosListos);
            actualizarPedidosEntregados(data.pedidosEntregados);
        })
        .catch(error => console.error('Error al cargar pedidos:', error));
    }

    // Actualizar contadores
    function actualizarContadores(contadores) {
        document.getElementById('contador-pendientes').textContent = contadores.pendientes;
        document.getElementById('contador-encocina').textContent = contadores.en_cocina;
        document.getElementById('contador-listos').textContent = contadores.listos;
        document.getElementById('contador-entregados').textContent = contadores.entregados;
    }

    // Toggle grupo de mesa
    function toggleMesaGrupo(mesaId) {
        var body    = document.getElementById('body-' + mesaId);
        var chevron = document.getElementById('chevron-' + mesaId);
        if (!body) return;
        var hidden = body.style.display === 'none';
        body.style.display      = hidden ? '' : 'none';
        chevron.style.transform = hidden ? 'rotate(-180deg)' : '';
    }

    // Generar HTML de un item de pedido dentro de una mesa
    function generarItemHTML(pedido) {
        const badgeClass  = pedido.estado === 'en_cocina' ? 'bg-danger' : 'bg-warning text-dark';
        const estadoTexto = pedido.estado === 'en_cocina' ? 'En Cocina' : 'Pendiente';
        const btnEnCocina = pedido.estado === 'pendiente'
            ? `<button type="button" class="btn btn-sm btn-outline-danger" onclick="marcarEnCocina(${pedido.id})"><i class="bi bi-fire"></i></button>`
            : '';
        const obsHTML = pedido.observacion
            ? `<div class="observacion-text mt-1"><i class="bi bi-chat-dots"></i> ${pedido.observacion}</div>`
            : '';

        return `
            <div class="pedido-item d-flex justify-content-between align-items-center py-2 px-3 border-bottom" id="pedido-${pedido.id}">
                <div>
                    <span class="fw-bold">${pedido.amount}x ${pedido.producto_nombre}</span>
                    ${obsHTML}
                    <div class="mt-1">
                        <small class="text-muted"><i class="bi bi-clock"></i> ${pedido.record}</small>
                        <small class="text-muted ms-2"><i class="bi bi-person"></i> ${pedido.mesero_nombre}</small>
                        <span class="badge ${badgeClass} ms-1">${estadoTexto}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0 ms-2">
                    ${btnEnCocina}
                    <button type="button" class="btn-listo" onclick="marcarListo(${pedido.id})">
                        <i class="bi bi-check-lg"></i> Listo
                    </button>
                </div>
            </div>
        `;
    }

    // Actualizar lista agrupada por mesa
    function actualizarPedidosPendientes(pedidos) {
        const container  = document.getElementById('pedidos-pendientes-container');
        const headerCount = document.getElementById('pedidos-count');

        if (pedidos.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 4rem;"></i>
                    <h3 class="text-success mt-3">¡Todo al día!</h3>
                    <p class="text-muted">No hay pedidos pendientes</p>
                </div>`;
            headerCount.textContent = 0;
            return;
        }

        // Agrupar por table_id
        const mesas = {};
        pedidos.forEach(p => {
            if (!mesas[p.table_id]) mesas[p.table_id] = { nombre: p.mesa_nombre, items: [] };
            mesas[p.table_id].items.push(p);
        });

        // Recordar qué mesas estaban abiertas antes del refresh
        const abiertos = {};
        Object.keys(mesas).forEach(tid => {
            const b = document.getElementById('body-' + tid);
            if (b) abiertos[tid] = b.style.display !== 'none';
        });

        let html = '';
        Object.entries(mesas).forEach(([tid, grupo]) => {
            const pendientes = grupo.items.filter(i => i.estado === 'pendiente').length;
            const enCocina   = grupo.items.filter(i => i.estado === 'en_cocina').length;
            const estaAbierto = abiertos[tid] ?? false;
            const chevronRot  = estaAbierto ? 'rotate(-180deg)' : '';
            const bodyDisplay = estaAbierto ? '' : 'none';

            const badgesPend = pendientes > 0
                ? `<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> ${pendientes} pendiente${pendientes > 1 ? 's' : ''}</span>` : '';
            const badgesCoc = enCocina > 0
                ? `<span class="badge bg-danger"><i class="bi bi-fire"></i> ${enCocina} en cocina</span>` : '';

            let itemsHTML = grupo.items.map(p => generarItemHTML(p)).join('');

            html += `
                <div class="mesa-grupo mb-3" id="mesa-grupo-${tid}">
                    <div class="mesa-grupo-header d-flex justify-content-between align-items-center"
                         onclick="toggleMesaGrupo(${tid})" style="cursor:pointer; user-select:none;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary" style="font-size:1rem;"><i class="bi bi-table"></i> ${grupo.nombre}</span>
                            ${badgesPend}${badgesCoc}
                        </div>
                        <i class="bi bi-chevron-down" id="chevron-${tid}" style="font-size:1.1rem; transition:transform 0.2s; flex-shrink:0; transform:${chevronRot};"></i>
                    </div>
                    <div class="mesa-grupo-body" id="body-${tid}" style="display:${bodyDisplay};">
                        ${itemsHTML}
                    </div>
                </div>`;
        });

        container.innerHTML = html;
        headerCount.textContent = pedidos.length;
    }

    // Generar HTML de un pedido listo
    function generarPedidoListoHTML(pedido) {
        return `
            <div class="col-md-6 col-lg-4">
                <div class="pedido-card listo">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary">${pedido.mesa_nombre}</span>
                            <span class="badge bg-info ms-1"><i class="bi bi-person"></i> ${pedido.mesero_nombre}</span>
                        </div>
                        <span class="badge bg-success"><i class="bi bi-check"></i> Listo</span>
                    </div>
                    <h5 class="mb-0 mt-2">
                        <span class="badge bg-secondary">${pedido.amount}x</span>
                        ${pedido.producto_nombre}
                        <small class="text-muted">$${pedido.producto_precio}</small>
                    </h5>
                </div>
            </div>
        `;
    }

    // Actualizar lista de pedidos listos
    function actualizarPedidosListos(pedidosListos) {
        const section = document.getElementById('pedidos-listos-section');
        
        if (pedidosListos.length > 0) {
            let html = `
                <div class="card-custom fade-in">
                    <div class="card-header-custom" style="background: linear-gradient(135deg, #27ae60, #1e8449) !important;">
                        <h2><i class="bi bi-check-circle"></i> Últimos Listos</h2>
                    </div>
                    <div class="card-body-custom">
                        <div class="row">
            `;
            pedidosListos.forEach(pedido => {
                html += generarPedidoListoHTML(pedido);
            });
            html += '</div></div></div>';
            section.innerHTML = html;
        } else {
            section.innerHTML = '';
        }
    }

    // Generar HTML de un pedido entregado
    function generarPedidoEntregadoHTML(pedido) {
        const borderColor = pedido.facturado ? '#6c757d' : '#3498db';
        const opacity = pedido.facturado ? '0.7' : '0.9';
        const factIcon = pedido.facturado ? '<span class="badge bg-secondary"><i class="bi bi-receipt"></i></span>' : '';
        
        return `
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="pedido-card" style="border-left-color: ${borderColor}; opacity: ${opacity}; padding: 0.75rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary">${pedido.mesa_nombre}</span>
                            ${factIcon}
                        </div>
                        <small class="text-muted">${pedido.updated_at}</small>
                    </div>
                    <div class="mt-1">
                        <span class="badge bg-secondary">${pedido.amount}x</span>
                        <strong>${pedido.producto_nombre}</strong>
                    </div>
                    <small class="text-info"><i class="bi bi-person"></i> ${pedido.mesero_nombre}</small>
                </div>
            </div>
        `;
    }

    // Actualizar lista de pedidos entregados
    function actualizarPedidosEntregados(pedidosEntregados) {
        const section = document.getElementById('pedidos-entregados-section');
        const countEl = document.getElementById('entregados-count');
        
        if (pedidosEntregados.length > 0) {
            let html = `
                <div class="card-custom fade-in">
                    <div class="card-header-custom" style="background: linear-gradient(135deg, #6c757d, #495057) !important;">
                        <h2><i class="bi bi-check2-all"></i> Entregados Hoy (<span id="entregados-count">${pedidosEntregados.length}</span>)</h2>
                    </div>
                    <div class="card-body-custom" style="max-height: 300px; overflow-y: auto;">
                        <div class="row" id="entregados-container">
            `;
            pedidosEntregados.forEach(pedido => {
                html += generarPedidoEntregadoHTML(pedido);
            });
            html += '</div></div></div>';
            section.innerHTML = html;
        } else {
            section.innerHTML = '';
        }
    }

    // Marcar pedido como listo via AJAX
    function marcarListo(id) {
        const btn = event.target.closest('button');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

        fetch(`/cocina/${id}/listo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('success', data.message);
                cargarPedidos(); // Recargar datos
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al procesar la solicitud');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Listo';
        });
    }

    // Marcar pedido como en cocina via AJAX
    function marcarEnCocina(id) {
        const btn = event.target.closest('button');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch(`/cocina/${id}/en-cocina`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('success', data.message);
                cargarPedidos(); // Recargar datos
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al procesar la solicitud');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-fire"></i> En Cocina';
        });
    }

    // Mostrar notificación
    function mostrarNotificacion(tipo, mensaje) {
        const alertDiv = document.createElement('div');
        
        let alertClass = 'alert-success-custom';
        let iconClass = 'check-circle-fill';
        
        if (tipo === 'error') {
            alertClass = 'alert-danger-custom';
            iconClass = 'exclamation-circle-fill';
        } else if (tipo === 'info') {
            alertClass = 'alert-info-custom';
            iconClass = 'bell-fill';
        }
        
        alertDiv.className = `alert-custom ${alertClass} fade-in`;
        alertDiv.innerHTML = `
            <i class="bi bi-${iconClass} fs-5"></i>
            <span>${mensaje}</span>
        `;
        
        const container = document.querySelector('.container') || document.body;
        const titulo = document.querySelector('.page-title');
        if (titulo && titulo.parentElement) {
            titulo.parentElement.parentElement.insertBefore(alertDiv, titulo.parentElement.nextSibling);
        } else {
            container.prepend(alertDiv);
        }
        
        // Remover notificación después de 3 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Actualización manual desde botón header
    function actualizarManual() {
        const btn = document.getElementById('btn-actualizar');
        const icon = btn.querySelector('i');
        icon.classList.add('rotating');
        btn.disabled = true;
        
        cargarPedidos();
        
        setTimeout(() => {
            icon.classList.remove('rotating');
            btn.disabled = false;
        }, 1000);
    }

    // Actualización manual desde botón flotante
    document.querySelector('.refresh-btn').onclick = function(e) {
        e.preventDefault();
        this.classList.add('rotating');
        cargarPedidos();
        setTimeout(() => this.classList.remove('rotating'), 1000);
    };

    // Toggle sonido on/off
    function toggleSonido() {
        inicializarAudio();
        sonidoHabilitado = !sonidoHabilitado;
        actualizarBotonSonido();
        
        if (sonidoHabilitado) {
            // Reproducir sonido de prueba
            reproducirBeep(880, 0.1);
            mostrarNotificacion('success', 'Alertas de sonido activadas');
        } else {
            mostrarNotificacion('info', 'Alertas de sonido desactivadas');
        }
        
        // Ocultar banner
        document.getElementById('banner-sonido').style.display = 'none';
        
        // Guardar preferencia
        localStorage.setItem('cocina_sonido', sonidoHabilitado ? '1' : '0');
    }

    // Habilitar sonido desde banner
    function habilitarSonido() {
        inicializarAudio();
        sonidoHabilitado = true;
        actualizarBotonSonido();
        reproducirBeep(880, 0.1);
        mostrarNotificacion('success', 'Alertas de sonido activadas');
        document.getElementById('banner-sonido').style.display = 'none';
        localStorage.setItem('cocina_sonido', '1');
    }

    // Actualizar visual del botón de sonido
    function actualizarBotonSonido() {
        const btn = document.getElementById('btn-sonido');
        const icono = document.getElementById('icono-sonido');
        
        if (sonidoHabilitado) {
            btn.classList.remove('muted');
            icono.className = 'bi bi-volume-up-fill';
            btn.title = 'Desactivar sonido';
        } else {
            btn.classList.add('muted');
            icono.className = 'bi bi-volume-mute-fill';
            btn.title = 'Activar sonido';
        }
    }

    // Iniciar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar preferencia de sonido
        const sonidoGuardado = localStorage.getItem('cocina_sonido');
        if (sonidoGuardado === '0') {
            sonidoHabilitado = false;
        }
        actualizarBotonSonido();

        // Inicializar audio con CUALQUIER interacción — no requiere botón específico
        // Se llama cada vez para reanudar si el contexto fue suspendido
        ['click', 'touchstart', 'keydown', 'pointerdown'].forEach(function(evt) {
            document.addEventListener(evt, function() {
                inicializarAudio();
            });
        });
    });

    // Al volver a la pestaña: reanudar audio y recargar pedidos
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
            cargarPedidos();
            if (parpadeoInterval) {
                clearInterval(parpadeoInterval);
                document.title = 'Cocina - Villa Lupe';
            }
        }
    });
</script>

<style>
    .rotating {
        animation: rotate 1s linear;
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
