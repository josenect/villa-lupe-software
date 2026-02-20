@extends('layouts.app')

@section('title', 'Reportes — ' . \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe'))

@section('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .card-custom { box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection

@section('content')
@php
    $dataActual     = request()->get('data', 'facturas');
    $esCat          = str_starts_with($dataActual, 'cat-');
    $esCocina       = $dataActual === 'cocina';
    $esFacturas     = $dataActual === 'facturas';
    $esProductos    = $dataActual === 'productos';
    $currentSlug    = $esCat ? substr($dataActual, 4) : null;
    $currentCatData = $esCat && isset($categoriaData[$currentSlug]) ? $categoriaData[$currentSlug] : null;
    $currentCat     = $esCat ? $categorias->firstWhere('slug', $currentSlug) : null;
    $rangoExtra     = $desde !== $hasta ? '&hasta=' . $hasta : '';
    $baseUrl        = '/admin/facturas/' . $desde;
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 fade-in no-print gap-3">
    <h1 class="page-title mb-0">
        <i class="bi bi-graph-up"></i> Reportes
        <small class="text-muted fs-6 ms-2">
            {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}
        </small>
    </h1>
    <div class="d-flex flex-wrap gap-2 align-items-end">
        <div>
            <small class="text-muted d-block" style="font-size:0.75rem;">Desde</small>
            <input type="date" id="input-desde" class="form-control-custom" value="{{ $desde }}" style="width:auto;">
        </div>
        <div>
            <small class="text-muted d-block" style="font-size:0.75rem;">Hasta</small>
            <input type="date" id="input-hasta" class="form-control-custom" value="{{ $hasta }}" style="width:auto;">
        </div>
        <button type="button" class="btn-primary-custom" onclick="aplicarRango()">
            <i class="bi bi-search"></i> Buscar
        </button>
        <a id="btn-export-csv" href="/admin/facturas/{{ $desde }}/export-csv?data={{ $dataActual }}{{ $desde !== $hasta ? '&hasta='.$hasta : '' }}" class="btn-success-custom">
            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
        </a>
        <a href="/" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Inicio
        </a>
    </div>
</div>
<script>
var _dataActual = '{{ $dataActual }}';

function aplicarRango() {
    var desde = document.getElementById('input-desde').value;
    var hasta = document.getElementById('input-hasta').value;
    if (!desde) return;
    var url = '/admin/facturas/' + desde + '?data=' + _dataActual;
    if (hasta && hasta !== desde) url += '&hasta=' + hasta;
    window.location.href = url;
}

function actualizarBtnCSV() {
    var desde = document.getElementById('input-desde').value || '{{ $desde }}';
    var hasta = document.getElementById('input-hasta').value || '{{ $hasta }}';
    var url = '/admin/facturas/' + desde + '/export-csv?data=' + _dataActual;
    if (hasta && hasta !== desde) url += '&hasta=' + hasta;
    var btn = document.getElementById('btn-export-csv');
    if (btn) btn.href = url;
}

document.getElementById('input-desde')?.addEventListener('change', actualizarBtnCSV);
document.getElementById('input-hasta')?.addEventListener('change', actualizarBtnCSV);
document.getElementById('input-desde')?.addEventListener('keydown', function(e){ if(e.key==='Enter') aplicarRango(); });
document.getElementById('input-hasta')?.addEventListener('keydown', function(e){ if(e.key==='Enter') aplicarRango(); });
</script>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in no-print">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-custom alert-error-custom fade-in no-print">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- Navegación de Reportes -->
<div class="card-custom mb-4 fade-in no-print">
    <div class="card-body-custom pb-2">
        {{-- Fijos: Facturas + Todos los Productos + Imprimir --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ $baseUrl }}?data=facturas{{ $rangoExtra }}"
               class="{{ $esFacturas ? 'btn-primary-custom' : 'btn-secondary-custom' }}">
                <i class="bi bi-receipt"></i> Facturas
            </a>
            <a href="{{ $baseUrl }}?data=productos{{ $rangoExtra }}"
               class="{{ $esProductos ? 'btn-primary-custom' : 'btn-secondary-custom' }}">
                <i class="bi bi-box-seam"></i> Todos los Productos
            </a>
            <a href="/visual-reporte/{{ $desde }}?data={{ $dataActual }}{{ $rangoExtra }}" target="_blank" class="btn-warning-custom ms-auto">
                <i class="bi bi-printer"></i> Imprimir Ticket
            </a>
        </div>

        {{-- Lista de categorías (scrolleable) --}}
        @if($categorias->count() > 0)
        <div class="border-top pt-2">
            <small class="text-muted d-block mb-2"><i class="bi bi-folder2"></i> Categorías:</small>
            <div class="d-flex flex-wrap gap-2" style="max-height: 90px; overflow-y: auto;">
                @foreach($categorias as $cat)
                <a href="{{ $baseUrl }}?data=cat-{{ $cat->slug }}{{ $rangoExtra }}"
                   class="{{ $dataActual == 'cat-'.$cat->slug ? 'btn-primary-custom' : 'btn-secondary-custom' }}"
                   style="font-size: 0.82rem; padding: 4px 10px;">
                    @if($cat->es_cocina)<i class="bi bi-fire"></i>@else<i class="bi bi-folder"></i>@endif
                    {{ $cat->nombre }}
                </a>
                @endforeach
                @if($categorias->where('es_cocina', true)->count() > 1)
                <a href="{{ $baseUrl }}?data=cocina{{ $rangoExtra }}"
                   class="{{ $esCocina ? 'btn-primary-custom' : 'btn-secondary-custom' }}"
                   style="font-size: 0.82rem; padding: 4px 10px;">
                    <i class="bi bi-fire"></i> Toda la Cocina
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ── Resumen solo para FACTURAS ─────────────────────────────────────────── --}}
@if($esFacturas)
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                <h3 class="text-success mt-2 mb-0">$ {{ number_format($facturasTotal, 0, ',', '.') }}</h3>
                <small class="text-muted">Total Ventas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-receipt text-primary" style="font-size: 2rem;"></i>
                <h3 class="text-primary mt-2 mb-0">{{ $facturas->where('estado', 'activa')->count() }}</h3>
                <small class="text-muted">Facturas Activas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-box text-info" style="font-size: 2rem;"></i>
                <h3 class="text-info mt-2 mb-0">{{ $totalProductos }}</h3>
                <small class="text-muted">Productos Vendidos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                <h3 class="text-danger mt-2 mb-0">{{ $facturas->whereIn('estado', ['anulada', 'reabierta'])->count() }}</h3>
                <small class="text-muted">Facturas Anuladas</small>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-4">
        <div class="card-custom h-100" style="border-left: 4px solid #27ae60;">
            <div class="card-body-custom text-center">
                <i class="bi bi-cash text-success" style="font-size: 2rem;"></i>
                <h3 class="text-success mt-2 mb-0">$ {{ number_format($totalEfectivo, 0, ',', '.') }}</h3>
                <small class="text-muted">Total Efectivo</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100" style="border-left: 4px solid #3498db;">
            <div class="card-body-custom text-center">
                <i class="bi bi-phone text-primary" style="font-size: 2rem;"></i>
                <h3 class="text-primary mt-2 mb-0">$ {{ number_format($totalTransferencia, 0, ',', '.') }}</h3>
                <small class="text-muted">Total Transferencia</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100" style="border-left: 4px solid #e74c3c;">
            <div class="card-body-custom text-center">
                <i class="bi bi-heart text-danger" style="font-size: 2rem;"></i>
                <h3 class="text-danger mt-2 mb-0">$ {{ number_format($propinaTotal, 0, ',', '.') }}</h3>
                <small class="text-muted">Total Propinas</small>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Resumen para CATEGORÍA o COCINA (solo ventas + productos de esa cat) ── --}}
@if($esCat && $currentCatData)
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-6">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                <h3 class="text-success mt-2 mb-0">$ {{ number_format($currentCatData['totalPrecio'], 0, ',', '.') }}</h3>
                <small class="text-muted">Ventas — {{ $currentCat->nombre ?? '' }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-box text-info" style="font-size: 2rem;"></i>
                <h3 class="text-info mt-2 mb-0">{{ $currentCatData['totalProductos'] }}</h3>
                <small class="text-muted">Productos — {{ $currentCat->nombre ?? '' }}</small>
            </div>
        </div>
    </div>
</div>
@endif

@if($esCocina)
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-6">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                <h3 class="text-success mt-2 mb-0">$ {{ number_format($cocinaTodo['totalPrecio'], 0, ',', '.') }}</h3>
                <small class="text-muted">Ventas — Toda la Cocina</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center">
                <i class="bi bi-box text-info" style="font-size: 2rem;"></i>
                <h3 class="text-info mt-2 mb-0">{{ $cocinaTodo['totalProductos'] }}</h3>
                <small class="text-muted">Productos — Toda la Cocina</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- REPORTE DE FACTURAS -->
@if($esFacturas)
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-receipt"></i> Facturas — {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}</h2>
    </div>
    <div class="card-body-custom">
        @if($facturas->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> Factura</th>
                            <th><i class="bi bi-table"></i> Mesa</th>
                            <th><i class="bi bi-cash"></i> Valor</th>
                            <th><i class="bi bi-heart"></i> Propina</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                            <th><i class="bi bi-credit-card"></i> Pago</th>
                            <th><i class="bi bi-clock"></i> Hora</th>
                            <th><i class="bi bi-toggle-on"></i> Estado</th>
                            <th class="no-print"><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facturas as $factura)
                            <tr class="{{ $factura->estado !== 'activa' ? 'table-secondary' : '' }}">
                                <td>
                                    <strong>{{ $factura->numero_factura }}</strong>
                                </td>
                                <td>{{ $factura->mesa->name ?? 'N/A' }}</td>
                                <td>$ {{ number_format($factura->valor_total, 0, ',', '.') }}</td>
                                <td>$ {{ number_format($factura->valor_propina, 0, ',', '.') }}</td>
                                <td>
                                    @if($factura->estado === 'activa')
                                        <strong class="text-success">$ {{ number_format($factura->valor_pagado, 0, ',', '.') }}</strong>
                                    @else
                                        <del class="text-muted">$ {{ number_format($factura->valor_pagado, 0, ',', '.') }}</del>
                                    @endif
                                </td>
                                <td>
                                    @if($factura->medio_pago === 'Mixto')
                                        <span class="badge bg-warning text-dark" title="Efectivo: ${{ number_format($factura->valor_efectivo, 0, ',', '.') }} | Transf: ${{ number_format($factura->valor_transferencia, 0, ',', '.') }}">
                                            <i class="bi bi-cash"></i>/<i class="bi bi-phone"></i> Mixto
                                        </span>
                                    @elseif($factura->medio_pago === 'Transferencia')
                                        <span class="badge bg-primary"><i class="bi bi-phone"></i> Transf.</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-cash"></i> Efectivo</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ date('H:i', strtotime($factura->fecha_hora_factura)) }}</small>
                                </td>
                                <td>
                                    @if($factura->estado === 'activa')
                                        <span class="status-badge activo">
                                            <i class="bi bi-check-circle"></i> Activa
                                        </span>
                                    @elseif($factura->estado === 'anulada')
                                        <span class="status-badge" style="background-color: rgba(231, 76, 60, 0.15); color: #e74c3c;">
                                            <i class="bi bi-x-circle"></i> Anulada
                                        </span>
                                    @else
                                        <span class="status-badge" style="background-color: rgba(243, 156, 18, 0.15); color: #f39c12;">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reabierta
                                        </span>
                                    @endif
                                </td>
                                <td class="no-print">
                                    <div class="action-buttons">
                                        <a href="{{ route('factura.visual', $factura->numero_factura) }}" target="_blank" class="btn-primary-custom btn-sm-custom" title="Ver Factura">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($factura->estado === 'activa')
                                            <a href="{{ route('admin.factura.showAnular', $factura->id) }}" class="btn-danger-custom btn-sm-custom" title="Anular/Reabrir">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" class="text-end"><strong>TOTAL FACTURAS ACTIVAS:</strong></td>
                            <td colspan="5"><strong class="fs-5">$ {{ number_format($facturasTotal, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay facturas para esta fecha</p>
            </div>
        @endif
    </div>
</div>
@endif

<!-- REPORTE DE PRODUCTOS -->
@if($esProductos)
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-box-seam"></i> Productos Vendidos — {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}</h2>
    </div>
    <div class="card-body-custom">
        @if(count($detalleElementos) > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> Cantidad</th>
                            <th><i class="bi bi-box"></i> Producto</th>
                            <th><i class="bi bi-folder"></i> Categoría</th>
                            <th><i class="bi bi-currency-dollar"></i> Precio Unit.</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalleElementos as $producto)
                            <tr>
                                <td><span class="badge bg-primary fs-6">{{ $producto->cantidad }}</span></td>
                                <td><strong>{{ $producto->name }}</strong></td>
                                <td><span class="badge bg-info">{{ $producto->category }}</span></td>
                                <td>$ {{ number_format($producto->precio - $producto->descuento, 0, ',', '.') }}</td>
                                <td><strong class="text-success">$ {{ number_format(($producto->precio - $producto->descuento) * $producto->cantidad, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>{{ $totalProductos }}</strong></td>
                            <td colspan="3" class="text-end"><strong>TOTAL PRODUCTOS:</strong></td>
                            <td><strong class="fs-5">$ {{ number_format($totalPrecio, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay productos vendidos para esta fecha</p>
            </div>
        @endif
    </div>
</div>
@endif

<!-- REPORTE POR CATEGORÍA ESPECÍFICA -->
@foreach($categorias as $cat)
@if($esCat && $currentSlug === $cat->slug)
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, {{ $cat->es_cocina ? '#e67e22, #d35400' : '#2980b9, #2471a3' }}) !important;">
        <h2>
            @if($cat->es_cocina)<i class="bi bi-fire"></i>@else<i class="bi bi-folder"></i>@endif
            {{ $cat->nombre }} — {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}
        </h2>
    </div>
    <div class="card-body-custom">
        @if(count($categoriaData[$cat->slug]['productos']) > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> Cantidad</th>
                            <th><i class="bi bi-box"></i> Producto</th>
                            <th><i class="bi bi-currency-dollar"></i> Precio Unit.</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categoriaData[$cat->slug]['productos'] as $p)
                            <tr>
                                <td><span class="badge bg-warning text-dark fs-6">{{ $p->cantidad }}</span></td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>$ {{ number_format($p->precio - $p->descuento, 0, ',', '.') }}</td>
                                <td><strong class="text-success">$ {{ number_format(($p->precio - $p->descuento) * $p->cantidad, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>{{ $categoriaData[$cat->slug]['totalProductos'] }}</strong></td>
                            <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong class="fs-5">$ {{ number_format($categoriaData[$cat->slug]['totalPrecio'], 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay productos de esta categoría para esta fecha</p>
            </div>
        @endif
    </div>
</div>
@endif
@endforeach

<!-- REPORTE TODA LA COCINA (todas las categorías es_cocina combinadas) -->
@if($esCocina)
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;">
        <h2><i class="bi bi-fire"></i> Toda la Cocina — {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}</h2>
    </div>
    <div class="card-body-custom">
        @if(count($cocinaTodo['productos']) > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> Cantidad</th>
                            <th><i class="bi bi-box"></i> Producto</th>
                            <th><i class="bi bi-folder"></i> Categoría</th>
                            <th><i class="bi bi-currency-dollar"></i> Precio Unit.</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cocinaTodo['productos'] as $p)
                            <tr>
                                <td><span class="badge bg-purple fs-6" style="background-color: #9b59b6;">{{ $p->cantidad }}</span></td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td><span class="badge bg-info">{{ $p->category }}</span></td>
                                <td>$ {{ number_format($p->precio - $p->descuento, 0, ',', '.') }}</td>
                                <td><strong class="text-success">$ {{ number_format(($p->precio - $p->descuento) * $p->cantidad, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>{{ $cocinaTodo['totalProductos'] }}</strong></td>
                            <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong class="fs-5">$ {{ number_format($cocinaTodo['totalPrecio'], 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay productos de cocina para esta fecha</p>
            </div>
        @endif
    </div>
</div>
@endif

@endsection
