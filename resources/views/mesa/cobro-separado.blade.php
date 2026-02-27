@extends('layouts.app')

@section('title', 'Cobro Separado — ' . $mesa->name)

@section('styles')
<style>
    .producto-row { cursor: pointer; transition: background 0.15s; }
    .producto-row:hover { background: #f0f7ff; }
    .producto-row.seleccionado { background: #e8f5e9; }
    .producto-row.seleccionado td { font-weight: 600; }
    .check-col { width: 70px; text-align: center; }
    .qty-input {
        width: 52px;
        text-align: center;
        font-size: 0.95rem;
        font-weight: bold;
        border: 1.5px solid #dee2e6;
        border-radius: 6px;
        padding: 2px 4px;
        transition: border-color 0.15s;
    }
    .qty-input:focus { outline: none; border-color: #27ae60; box-shadow: 0 0 0 2px #27ae6033; }
    .qty-input.activo { border-color: #27ae60; background: #f0fff4; }
    .total-seleccion {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    .sin-seleccion { color: #aaa; font-size: 0.85rem; }
    .pago-box { padding: 6px 8px; border-radius: 10px; background: #f8f9fa; }
    .pago-box.efectivo    { border: 2px solid #27ae60; }
    .pago-box.transferencia { border: 2px solid #3498db; }
    .de-total { color: #999; font-size: 0.8rem; }

    /* Sobreescribe max-width: 60px que el layout global aplica en móvil */
    .btn-cobro-page {
        max-width: none !important;
        flex: 0 0 auto !important;
        white-space: nowrap;
    }

    /* ── Mobile compacto ─────────────────────────── */
    @media (max-width: 575px) {
        .page-title { font-size: 0.9rem !important; }
        .card-header-custom { flex-wrap: wrap; gap: 6px; }
        .card-header-custom h2 { font-size: 0.85rem; }
        .check-col { width: 50px; }
        .qty-input { width: 42px; font-size: 0.85rem; padding: 1px 2px; }
        .total-seleccion { font-size: 1.2rem; }
        #display-total { font-size: 1.5rem !important; }
        .pendiente-item { font-size: 0.8rem; }
    }
</style>
@endsection

@section('content')
@php
    $propinaHabilitada = \App\Models\Setting::get('propina_habilitada', '1') === '1';
    $propinaPct        = (int) \App\Models\Setting::get('propina_porcentaje', env('PROPINA', 10));
@endphp

<div class="d-flex align-items-center gap-2 mb-3 fade-in">
    <a href="/mesa/{{ $mesa->id }}" class="btn-secondary-custom btn-sm-custom btn-cobro-page">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <h1 class="mb-0 page-title flex-grow-1 text-center" style="font-size: clamp(0.8rem, 3.5vw, 1.3rem);">
        <i class="bi bi-people text-warning"></i> Cobro Separado — {{ $mesa->name }}
    </h1>
</div>

@if($productosTable->isEmpty())
    <div class="card-custom text-center py-5 fade-in">
        <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
        <h4 class="mt-3 text-muted">Todos los productos han sido cobrados</h4>
        <a href="/mesa/{{ $mesa->id }}" class="btn-primary-custom mt-3">Ir a la mesa</a>
    </div>
@else

<div class="row g-3 fade-in">
    {{-- Lista de productos --}}
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="bi bi-list-check"></i> <span class="d-none d-sm-inline">Indica cuánto paga </span>Esta cuenta</h2>
                <div class="d-flex gap-2" style="flex-shrink:0;">
                    <button type="button" id="btn-todos" class="btn-primary-custom btn-sm-custom btn-cobro-page">Todos</button>
                    <button type="button" id="btn-ninguno" class="btn-secondary-custom btn-sm-custom btn-cobro-page">Ninguno</button>
                </div>
            </div>
            <div class="card-body-custom p-0">
                <div style="overflow-x:auto;">
                    <table class="table-custom mb-0" id="tabla-productos">
                        <thead>
                            <tr>
                                <th class="check-col" title="Cantidad a cobrar en esta cuenta">Qty</th>
                                <th>Producto</th>
                                <th class="text-center d-none d-sm-table-cell">En mesa</th>
                                <th class="text-end d-none d-sm-table-cell">P. unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosTable as $item)
                            @php $unitPrice = $item->price - $item->dicount; @endphp
                            <tr class="producto-row"
                                data-id="{{ $item->id }}"
                                data-unit-price="{{ $unitPrice }}"
                                data-max="{{ $item->amount }}">
                                <td class="check-col" onclick="event.stopPropagation()">
                                    <input type="number" class="qty-input"
                                        data-id="{{ $item->id }}"
                                        min="0" max="{{ $item->amount }}" value="0"
                                        inputmode="numeric">
                                </td>
                                <td>
                                    <strong>{{ $item->producto->name }}</strong>
                                    <span class="d-sm-none text-muted small"> ({{ $item->amount }})</span>
                                    @if($item->observacion)
                                        <br><small class="text-muted"><i class="bi bi-chat-dots"></i> {{ $item->observacion }}</small>
                                    @endif
                                </td>
                                <td class="text-center d-none d-sm-table-cell">
                                    <span class="badge bg-secondary">{{ $item->amount }}</span>
                                </td>
                                <td class="text-end d-none d-sm-table-cell">$ {{ number_format($unitPrice, 0, ',', '.') }}</td>
                                <td class="text-end" id="row-sub-{{ $item->id }}">$ 0</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel resumen --}}
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <h2 class="mb-0"><i class="bi bi-calculator"></i> Esta cuenta</h2>
            </div>
            <div class="card-body-custom text-center">
                <div class="text-muted small mb-1">Subtotal seleccionado</div>
                <div class="total-seleccion" id="display-subtotal">$ 0</div>

                @if($propinaHabilitada)
                <div class="text-muted small mt-2">Propina sugerida ({{ $propinaPct }}%)</div>
                <div class="fw-semibold text-danger" id="display-propina">$ 0</div>
                @endif

                <hr>
                <div class="text-muted small">Total a cobrar</div>
                <div style="font-size:2rem; font-weight:800; color:#27ae60;" id="display-total">$ 0</div>
                <div class="sin-seleccion mt-1" id="msg-sin-seleccion">Indica la cantidad a cobrar</div>

                <div class="d-flex flex-column gap-2 mt-3" id="btns-accion" style="display:none!important;">
                    <button type="button" id="btn-preliminar" class="btn-warning-custom w-100 justify-content-center">
                        <i class="bi bi-printer"></i> Preliminar
                    </button>
                    <button type="button" id="btn-facturar-parcial" class="btn-success-custom w-100 justify-content-center">
                        <i class="bi bi-receipt"></i> Cobrar esta cuenta
                    </button>
                </div>
            </div>
        </div>

        <div class="card-custom mt-3" id="card-pendientes">
            <div class="card-header-custom py-2 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #e67e22, #d35400) !important; cursor:pointer;" onclick="togglePendientes()">
                <h6 class="mb-0"><i class="bi bi-hourglass-split"></i> Pendiente: <span id="total-pendiente-valor">$ {{ number_format($totalMesa, 0, ',', '.') }}</span></h6>
                <i class="bi bi-chevron-down" id="chevron-pendientes" style="transition:transform .2s;"></i>
            </div>
            <div id="lista-pendientes" class="card-body-custom py-1" style="max-height:200px;overflow-y:auto;">
                <div class="text-center py-2">
                    <small class="text-muted">Selecciona productos para ver qué queda</small>
                </div>
            </div>
            <div class="text-center py-2" id="btn-imprimir-pendientes-wrap" style="display:none;">
                <button type="button" id="btn-imprimir-pendientes" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-printer"></i> Imprimir pendientes
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal pago parcial --}}
<div class="modal fade" id="modalPagoParcial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none;">
            <div class="modal-header py-2" style="background:linear-gradient(135deg,#27ae60,#1e8449);color:white;border-radius:16px 16px 0 0;">
                <div>
                    <h5 class="modal-title mb-0"><i class="bi bi-receipt"></i> Cobrar cuenta</h5>
                    <div class="small opacity-75">{{ $mesa->name }}</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Propina --}}
                @if($propinaHabilitada)
                <div class="mb-2">
                    <label class="form-label-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-heart text-danger"></i> Propina</span>
                        <button type="button" id="btn-toggle-propina-parcial"
                            style="font-size:0.75rem;padding:2px 10px;border-radius:20px;border:1.5px solid #27ae60;background:#27ae60;color:white;cursor:pointer;line-height:1.8;transition:all 0.2s;">
                            <i class="bi bi-check-circle-fill"></i> Con propina
                        </button>
                    </label>
                    <input type="text" id="parcial-propina" class="form-control-custom" inputmode="numeric" autocomplete="off" value="">
                    <input type="hidden" id="parcial-propina-hidden" value="0">
                    <small class="text-muted" id="parcial-propina-hint"></small>
                </div>
                @else
                <input type="hidden" id="parcial-propina" value="">
                <input type="hidden" id="parcial-propina-hidden" value="0">
                @endif

                {{-- Forma de pago --}}
                <div class="mb-2">
                    <label class="form-label-custom"><i class="bi bi-credit-card"></i> Forma de Pago</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="pago-box efectivo">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small text-success mb-0"><i class="bi bi-cash-stack"></i> Efectivo</label>
                                    <button type="button" id="parcial-todo-efectivo"
                                        style="font-size:0.7rem;padding:1px 7px;border-radius:6px;border:1px solid #27ae60;background:#27ae60;color:white;cursor:pointer;line-height:1.6;">Todo</button>
                                </div>
                                <input type="text" id="parcial-efectivo" class="form-control-custom" inputmode="numeric" autocomplete="off" value="">
                                <input type="hidden" id="parcial-efectivo-hidden" value="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pago-box transferencia">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small text-primary mb-0"><i class="bi bi-phone"></i> Transferencia</label>
                                    <button type="button" id="parcial-todo-transferencia"
                                        style="font-size:0.7rem;padding:1px 7px;border-radius:6px;border:1px solid #3498db;background:#3498db;color:white;cursor:pointer;line-height:1.6;">Todo</button>
                                </div>
                                <input type="text" id="parcial-transferencia" class="form-control-custom" inputmode="numeric" autocomplete="off" value="">
                                <input type="hidden" id="parcial-transferencia-hidden" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paga con --}}
                <div class="mb-2">
                    <label class="form-label-custom mb-1">
                        <i class="bi bi-cash-coin"></i> Paga con
                        <small class="text-muted fw-normal">(para calcular vueltas)</small>
                        <span id="parcial-vueltas-wrap" class="d-none ms-2">
                            <span class="fw-semibold" id="parcial-vueltas-label">Vueltas:</span>
                            <span id="parcial-vueltas-valor" class="fw-bold ms-1"></span>
                        </span>
                    </label>
                    <input type="text" id="parcial-paga-con" class="form-control-custom" inputmode="numeric" autocomplete="off" placeholder="$ 0">
                </div>

                {{-- Resumen --}}
                <div class="bg-light p-2 rounded-3 small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Sub: <span id="parcial-display-sub">$ 0</span> + Prop: <span id="parcial-display-prop">$ 0</span></span>
                        <strong class="text-success fs-6" id="parcial-display-total">$ 0</strong>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-cash-stack text-success"></i> Efect: <span id="parcial-resumen-efectivo">$ 0</span></span>
                        <span><i class="bi bi-phone text-primary"></i> Trans: <span id="parcial-resumen-transferencia">$ 0</span></span>
                    </div>
                </div>

                <div id="parcial-alerta" class="alert alert-danger mt-2 mb-0 d-none" style="font-size:0.85rem;">
                    <i class="bi bi-exclamation-triangle"></i> La suma debe ser igual al total a pagar.
                </div>
            </div>
            <div class="modal-footer py-2" style="border:none;">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button type="button" class="btn-success-custom" id="confirmar-parcial-btn">
                    <i class="bi bi-check-lg"></i> Confirmar Factura
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
var mesaId     = {{ $mesa->id }};
var propinaPct = {{ $propinaPct }};
var propinaOn  = {{ $propinaHabilitada ? 'true' : 'false' }};

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmt(v) { return '$ ' + Math.round(v).toLocaleString('es-CO'); }
function setMoney(dId, hId, v) {
    v = Math.max(0, Math.round(v));
    document.getElementById(dId).value = v > 0 ? '$ ' + v.toLocaleString('es-CO') : '';
    document.getElementById(hId).value = v;
}
function getMoney(hId) { return parseFloat(document.getElementById(hId).value) || 0; }
function addMoneyHandlers(dId, hId, cb) {
    var el = document.getElementById(dId);
    if (!el) return;
    el.addEventListener('input', function() {
        var n = this.value.replace(/\D/g,'');
        document.getElementById(hId).value = n || '0';
        this.value = n ? '$ ' + Number(n).toLocaleString('es-CO') : '';
        if (cb) cb();
    });
    el.addEventListener('keydown', function(e) {
        var ok = ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'];
        if (ok.includes(e.key)) return;
        if ((e.ctrlKey||e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
        if (e.key < '0' || e.key > '9') e.preventDefault();
    });
}

// ── Estado selección ──────────────────────────────────────────────────────────
var subtotalSeleccion = 0;
var propinaSugerida   = 0;

function recalcularSeleccion() {
    subtotalSeleccion = 0;

    document.querySelectorAll('.qty-input').forEach(function(inp) {
        var qty       = Math.max(0, parseInt(inp.value) || 0);
        var maxQty    = parseInt(inp.max) || 0;
        qty = Math.min(qty, maxQty);
        inp.value = qty; // clamp

        var row       = inp.closest('tr');
        var unitPrice = parseFloat(row.dataset.unitPrice) || 0;
        var rowSub    = qty * unitPrice;

        subtotalSeleccion += rowSub;
        row.classList.toggle('seleccionado', qty > 0);
        inp.classList.toggle('activo', qty > 0);

        // Actualizar subtotal de fila
        var rowSubEl = document.getElementById('row-sub-' + inp.dataset.id);
        if (rowSubEl) rowSubEl.textContent = qty > 0 ? fmt(rowSub) : '$ 0';
    });

    propinaSugerida = propinaOn
        ? Math.floor(subtotalSeleccion * propinaPct / 100 / 1000) * 1000
        : 0;
    var total = subtotalSeleccion + propinaSugerida;

    document.getElementById('display-subtotal').textContent = fmt(subtotalSeleccion);
    var dp = document.getElementById('display-propina');
    if (dp) dp.textContent = fmt(propinaSugerida);
    document.getElementById('display-total').textContent = fmt(total);

    // Actualizar lista de pendientes
    actualizarPendientes();

    var haySeleccion = subtotalSeleccion > 0;
    document.getElementById('msg-sin-seleccion').style.display = haySeleccion ? 'none' : '';
    document.getElementById('btns-accion').style.display = haySeleccion ? 'flex' : 'none';
    document.getElementById('btns-accion').style.flexDirection = 'column';
}

// Clic en fila → toggle entre 0 y max
document.querySelectorAll('.producto-row').forEach(function(row) {
    row.addEventListener('click', function(e) {
        if (e.target.tagName === 'INPUT') return;
        var inp = this.querySelector('.qty-input');
        var maxQty = parseInt(inp.max) || 0;
        inp.value = (parseInt(inp.value) || 0) > 0 ? 0 : maxQty;
        recalcularSeleccion();
    });
});

// Cambio manual en el input
document.querySelectorAll('.qty-input').forEach(function(inp) {
    inp.addEventListener('input', recalcularSeleccion);
    inp.addEventListener('change', recalcularSeleccion);
});

// Todos / Ninguno
document.getElementById('btn-todos')?.addEventListener('click', function() {
    document.querySelectorAll('.qty-input').forEach(function(inp) { inp.value = inp.max; });
    recalcularSeleccion();
});
document.getElementById('btn-ninguno')?.addEventListener('click', function() {
    document.querySelectorAll('.qty-input').forEach(function(inp) { inp.value = 0; });
    recalcularSeleccion();
});

// ── Obtener items seleccionados (id → qty) ────────────────────────────────────
function itemsSeleccionados() {
    var ids = [], qtys = [];
    document.querySelectorAll('.qty-input').forEach(function(inp) {
        var qty = parseInt(inp.value) || 0;
        if (qty > 0) {
            ids.push(inp.dataset.id);
            qtys.push(qty);
        }
    });
    return { ids: ids, qtys: qtys };
}

// ── Preliminar parcial ────────────────────────────────────────────────────────
document.getElementById('btn-preliminar')?.addEventListener('click', function() {
    var items = itemsSeleccionados();
    if (!items.ids.length) return;
    window.open('/preliminar-parcial/' + mesaId
        + '?ids='  + items.ids.join(',')
        + '&qtys=' + items.qtys.join(','), '_blank');
});

// ── Items pendientes (restantes) ─────────────────────────────────────────────
function itemsPendientes() {
    var ids = [], qtys = [];
    document.querySelectorAll('.qty-input').forEach(function(inp) {
        var seleccionado = parseInt(inp.value) || 0;
        var max = parseInt(inp.max) || 0;
        var restante = max - seleccionado;
        if (restante > 0) {
            ids.push(inp.dataset.id);
            qtys.push(restante);
        }
    });
    return { ids: ids, qtys: qtys };
}

// ── Imprimir pendientes ──────────────────────────────────────────────────────
document.getElementById('btn-imprimir-pendientes')?.addEventListener('click', function() {
    var items = itemsPendientes();
    if (!items.ids.length) return;
    window.open('/preliminar-parcial/' + mesaId
        + '?ids='  + items.ids.join(',')
        + '&qtys=' + items.qtys.join(',')
        + '&tipo=pendientes', '_blank');
});

// ── Abrir modal de pago ───────────────────────────────────────────────────────
document.getElementById('btn-facturar-parcial')?.addEventListener('click', function() {
    var total = subtotalSeleccion + propinaSugerida;
    setMoney('parcial-propina',       'parcial-propina-hidden',       propinaSugerida);
    setMoney('parcial-efectivo',      'parcial-efectivo-hidden',      total);
    setMoney('parcial-transferencia', 'parcial-transferencia-hidden', 0);
    var hint = document.getElementById('parcial-propina-hint');
    if (hint) hint.textContent = propinaSugerida > 0
        ? 'Sugerida (' + propinaPct + '%): ' + fmt(propinaSugerida)
        : '';
    actualizarTotalesParcial();
    new bootstrap.Modal(document.getElementById('modalPagoParcial')).show();
});

// ── Lógica modal de pago parcial ──────────────────────────────────────────────
function actualizarTotalesParcial() {
    var propina  = getMoney('parcial-propina-hidden');
    var totalP   = subtotalSeleccion + propina;
    var efectivo = getMoney('parcial-efectivo-hidden');
    var transf   = getMoney('parcial-transferencia-hidden');
    var suma     = efectivo + transf;

    document.getElementById('parcial-display-sub').textContent   = fmt(subtotalSeleccion);
    document.getElementById('parcial-display-prop').textContent  = fmt(propina);
    document.getElementById('parcial-display-total').textContent = fmt(totalP);
    document.getElementById('parcial-resumen-efectivo').textContent      = fmt(efectivo);
    document.getElementById('parcial-resumen-transferencia').textContent = fmt(transf);

    var ok = Math.abs(suma - totalP) <= 0.01;
    document.getElementById('parcial-alerta').classList.toggle('d-none', ok);
    document.getElementById('confirmar-parcial-btn').disabled     = !ok;
    document.getElementById('confirmar-parcial-btn').style.opacity = ok ? '1' : '0.5';
}

addMoneyHandlers('parcial-propina', 'parcial-propina-hidden', function() {
    if (getMoney('parcial-transferencia-hidden') === 0)
        setMoney('parcial-efectivo','parcial-efectivo-hidden', subtotalSeleccion + getMoney('parcial-propina-hidden'));
    actualizarTotalesParcial();
});
var ajust = false;
addMoneyHandlers('parcial-efectivo','parcial-efectivo-hidden', function() {
    if (ajust) return; ajust = true;
    setMoney('parcial-transferencia','parcial-transferencia-hidden',
        Math.max(0, subtotalSeleccion + getMoney('parcial-propina-hidden') - getMoney('parcial-efectivo-hidden')));
    ajust = false; actualizarTotalesParcial();
});
addMoneyHandlers('parcial-transferencia','parcial-transferencia-hidden', function() {
    if (ajust) return; ajust = true;
    setMoney('parcial-efectivo','parcial-efectivo-hidden',
        Math.max(0, subtotalSeleccion + getMoney('parcial-propina-hidden') - getMoney('parcial-transferencia-hidden')));
    ajust = false; actualizarTotalesParcial();
});

document.getElementById('parcial-todo-efectivo')?.addEventListener('click', function() {
    var t = subtotalSeleccion + getMoney('parcial-propina-hidden');
    setMoney('parcial-efectivo','parcial-efectivo-hidden', t);
    setMoney('parcial-transferencia','parcial-transferencia-hidden', 0);
    actualizarTotalesParcial();
});
document.getElementById('parcial-todo-transferencia')?.addEventListener('click', function() {
    var t = subtotalSeleccion + getMoney('parcial-propina-hidden');
    setMoney('parcial-transferencia','parcial-transferencia-hidden', t);
    setMoney('parcial-efectivo','parcial-efectivo-hidden', 0);
    actualizarTotalesParcial();
});

document.getElementById('btn-toggle-propina-parcial')?.addEventListener('click', function() {
    var actual = getMoney('parcial-propina-hidden');
    var nueva  = actual > 0 ? 0 : propinaSugerida;
    setMoney('parcial-propina','parcial-propina-hidden', nueva);
    if (getMoney('parcial-transferencia-hidden') === 0)
        setMoney('parcial-efectivo','parcial-efectivo-hidden', subtotalSeleccion + nueva);
    actualizarTotalesParcial();
    if (nueva > 0) {
        this.style.background = '#27ae60'; this.style.borderColor = '#27ae60';
        this.innerHTML = '<i class="bi bi-check-circle-fill"></i> Con propina';
    } else {
        this.style.background = '#95a5a6'; this.style.borderColor = '#95a5a6';
        this.innerHTML = '<i class="bi bi-x-circle-fill"></i> Sin propina';
    }
});

// Vueltas
document.getElementById('parcial-paga-con')?.addEventListener('input', function() {
    var n = this.value.replace(/\D/g,'');
    this.value = n ? '$ ' + Number(n).toLocaleString('es-CO') : '';
    var pagaCon  = parseFloat(n) || 0;
    var efectivo = getMoney('parcial-efectivo-hidden');
    var wrap     = document.getElementById('parcial-vueltas-wrap');
    if (pagaCon <= 0) { wrap.classList.add('d-none'); return; }
    wrap.classList.remove('d-none');
    var vueltas = pagaCon - efectivo;
    document.getElementById('parcial-vueltas-label').textContent = vueltas >= 0 ? 'Vueltas:' : 'Falta:';
    document.getElementById('parcial-vueltas-valor').textContent = fmt(Math.abs(vueltas));
    document.getElementById('parcial-vueltas-valor').style.color = vueltas >= 0 ? '#27ae60' : '#e74c3c';
});

// ── Confirmar factura parcial ─────────────────────────────────────────────────
document.getElementById('confirmar-parcial-btn')?.addEventListener('click', function() {
    var items   = itemsSeleccionados();
    var propina = getMoney('parcial-propina-hidden');
    var efectivo= getMoney('parcial-efectivo-hidden');
    var transf  = getMoney('parcial-transferencia-hidden');
    var metodo  = efectivo > 0 && transf > 0 ? 'Mixto' : (transf > 0 ? 'Transferencia' : 'Efectivo');
    var url = '/generar-factura-parcial/' + mesaId
            + '?ids='           + items.ids.join(',')
            + '&qtys='          + items.qtys.join(',')
            + '&propina='       + propina
            + '&efectivo='      + efectivo
            + '&transferencia=' + transf
            + '&medio_pago='    + encodeURIComponent(metodo);
    window.open(url, '_blank');
    bootstrap.Modal.getInstance(document.getElementById('modalPagoParcial'))?.hide();
    setTimeout(function() { window.location.reload(); }, 800);
});

function togglePendientes() {
    var body = document.getElementById('lista-pendientes');
    var chevron = document.getElementById('chevron-pendientes');
    if (body.style.display === 'none') {
        body.style.display = '';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

function actualizarPendientes() {
    var container = document.getElementById('lista-pendientes');
    var totalPendienteEl = document.getElementById('total-pendiente-valor');
    if (!container) return;

    var pendientes = [];
    var totalPendiente = 0;

    document.querySelectorAll('.qty-input').forEach(function(inp) {
        var seleccionado = parseInt(inp.value) || 0;
        var max = parseInt(inp.max) || 0;
        var restante = max - seleccionado;
        if (restante > 0) {
            var row = inp.closest('tr');
            var unitPrice = parseFloat(row.dataset.unitPrice) || 0;
            var nombre = row.querySelector('td:nth-child(2) strong').textContent;
            var sub = restante * unitPrice;
            totalPendiente += sub;
            pendientes.push({ nombre: nombre, qty: restante, sub: sub });
        }
    });

    if (pendientes.length === 0) {
        container.innerHTML = '<div class="text-center py-2"><i class="bi bi-check-circle text-success"></i> <small class="text-success fw-bold">Todo cobrado</small></div>';
    } else {
        var html = '';
        pendientes.forEach(function(p) {
            html += '<div class="d-flex justify-content-between align-items-center py-1 px-1 border-bottom">'
                + '<span><span class="badge bg-secondary me-1">' + p.qty + 'x</span> ' + p.nombre + '</span>'
                + '<span class="fw-bold small">' + fmt(p.sub) + '</span>'
                + '</div>';
        });
        container.innerHTML = html;
    }

    if (totalPendienteEl) totalPendienteEl.textContent = fmt(totalPendiente);

    var btnWrap = document.getElementById('btn-imprimir-pendientes-wrap');
    if (btnWrap) {
        var haySeleccion = document.querySelectorAll('.qty-input').length > 0 &&
            Array.from(document.querySelectorAll('.qty-input')).some(function(inp) { return parseInt(inp.value) > 0; });
        btnWrap.style.display = (pendientes.length > 0 && haySeleccion) ? '' : 'none';
    }
}

recalcularSeleccion();
</script>
@endsection
