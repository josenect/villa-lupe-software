@extends('layouts.app')

@section('title', 'Reportes del Día - Villa Lupe')

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
<div class="d-flex justify-content-between align-items-center mb-4 fade-in no-print">
    <h1 class="page-title mb-0">
        <i class="bi bi-graph-up"></i> Reportes del Día
    </h1>
    <div class="d-flex gap-2 align-items-center">
        <form action="" method="GET" class="d-flex gap-2">
            <input type="date" name="fecha" value="{{ $date }}" class="form-control-custom" style="width: auto;" onchange="window.location.href='/admin/facturas/' + this.value">
        </form>
        <a href="/" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Inicio
        </a>
    </div>
</div>

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
    <div class="card-body-custom">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="/admin/facturas/{{ $date }}?data=facturas" class="btn-primary-custom {{ request()->get('data') == 'facturas' ? '' : 'btn-secondary-custom' }}">
                <i class="bi bi-receipt"></i> Facturas
            </a>
            <a href="/admin/facturas/{{ $date }}?data=productos" class="btn-primary-custom {{ request()->get('data') == 'productos' ? '' : 'btn-secondary-custom' }}">
                <i class="bi bi-box-seam"></i> Productos
            </a>
            <a href="/admin/facturas/{{ $date }}?data=cocina" class="btn-primary-custom {{ request()->get('data') == 'cocina' ? '' : 'btn-secondary-custom' }}">
                <i class="bi bi-egg-fried"></i> Cocina Almuerzos
            </a>
            <a href="/admin/facturas/{{ $date }}?data=cocina-productos" class="btn-primary-custom {{ request()->get('data') == 'cocina-productos' ? '' : 'btn-secondary-custom' }}">
                <i class="bi bi-cup-hot"></i> Cocina Productos
            </a>
            <a href="/visual-reporte/{{ $date }}?data={{ request()->get('data', 'facturas') }}" target="_blank" class="btn-warning-custom">
                <i class="bi bi-printer"></i> Imprimir Ticket
            </a>
        </div>
    </div>
</div>

<!-- Resumen Rápido -->
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

<!-- REPORTE DE FACTURAS -->
@if(!request()->has('data') || request()->get('data') === 'facturas')
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-receipt"></i> Facturas del {{ $date }}</h2>
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
                            <td colspan="4" class="text-end"><strong>TOTAL FACTURAS ACTIVAS:</strong></td>
                            <td colspan="4"><strong class="fs-5">$ {{ number_format($facturasTotal, 0, ',', '.') }}</strong></td>
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
@if(request()->has('data') && request()->get('data') === 'productos')
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-box-seam"></i> Productos Vendidos - {{ $date }}</h2>
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

<!-- REPORTE COCINA ALMUERZOS -->
@if(request()->has('data') && request()->get('data') === 'cocina')
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #e67e22, #d35400) !important;">
        <h2><i class="bi bi-egg-fried"></i> Cocina - Almuerzos - {{ $date }}</h2>
    </div>
    <div class="card-body-custom">
        @if(count($detalleCocinaAlmu) > 0)
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
                        @foreach ($detalleCocinaAlmu as $productoCocina)
                            <tr>
                                <td><span class="badge bg-warning text-dark fs-6">{{ $productoCocina->cantidad }}</span></td>
                                <td><strong>{{ $productoCocina->name }}</strong></td>
                                <td>$ {{ number_format($productoCocina->precio - $productoCocina->descuento, 0, ',', '.') }}</td>
                                <td><strong class="text-success">$ {{ number_format(($productoCocina->precio - $productoCocina->descuento) * $productoCocina->cantidad, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>{{ $cocinaTotalProductosAlmu }}</strong></td>
                            <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong class="fs-5">$ {{ number_format($cocinaTotalPrecioAlmu, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay almuerzos vendidos para esta fecha</p>
            </div>
        @endif
    </div>
</div>
@endif

<!-- REPORTE COCINA PRODUCTOS -->
@if(request()->has('data') && request()->get('data') === 'cocina-productos')
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;">
        <h2><i class="bi bi-cup-hot"></i> Cocina - Todos los Productos - {{ $date }}</h2>
    </div>
    <div class="card-body-custom">
        @if(count($detalleCocina) > 0)
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
                        @foreach ($detalleCocina as $productoCocina)
                            <tr>
                                <td><span class="badge bg-purple fs-6" style="background-color: #9b59b6;">{{ $productoCocina->cantidad }}</span></td>
                                <td><strong>{{ $productoCocina->name }}</strong></td>
                                <td>$ {{ number_format($productoCocina->precio - $productoCocina->descuento, 0, ',', '.') }}</td>
                                <td><strong class="text-success">$ {{ number_format(($productoCocina->precio - $productoCocina->descuento) * $productoCocina->cantidad, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>{{ $cocinaTotalProductos }}</strong></td>
                            <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong class="fs-5">$ {{ number_format($cocinaTotalPrecio, 0, ',', '.') }}</strong></td>
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
