@extends('layouts.app')

@section('title', 'Historial Domicilios')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 fade-in gap-3">
    <h1 class="page-title mb-0">
        <i class="bi bi-clock-history"></i> Historial Domicilios
        <small class="text-muted fs-6 ms-2">
            {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}
        </small>
    </h1>
    <a href="{{ route('domicilios.index') }}" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

{{-- Filtro de fechas --}}
<div class="card-custom mb-4 fade-in">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('domicilios.historial') }}" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <small class="text-muted d-block" style="font-size:0.75rem;">Desde</small>
                <input type="date" name="desde" class="form-control-custom" value="{{ $desde }}" style="width:auto;">
            </div>
            <div>
                <small class="text-muted d-block" style="font-size:0.75rem;">Hasta</small>
                <input type="date" name="hasta" class="form-control-custom" value="{{ $hasta }}" style="width:auto;">
            </div>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-search"></i> Buscar
            </button>
        </form>
    </div>
</div>

{{-- Domicilios Facturados --}}
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #27ae60, #219a52);">
        <h2><i class="bi bi-check-circle"></i> Facturados ({{ $facturados->count() }})</h2>
    </div>
    <div class="card-body-custom">
        @if($facturados->isEmpty())
            <p class="text-muted text-center py-3">No hay domicilios facturados en este periodo.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Telefono</th>
                            <th>Direccion</th>
                            <th>Factura</th>
                            <th>Total</th>
                            <th>Medio Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facturados as $dom)
                            @php $facturas = $dom->mesa ? $dom->mesa->facturas : collect(); @endphp
                            @if($facturas->isEmpty())
                                <tr>
                                    <td data-label="Cliente">{{ $dom->cliente_nombre }}</td>
                                    <td data-label="Telefono">{{ $dom->cliente_telefono }}</td>
                                    <td data-label="Direccion">{{ $dom->cliente_direccion }}</td>
                                    <td data-label="Factura" colspan="5"><span class="text-muted">Sin factura asociada</span></td>
                                </tr>
                            @else
                                @foreach($facturas as $factura)
                                    <tr>
                                        <td data-label="Cliente">{{ $dom->cliente_nombre }}</td>
                                        <td data-label="Telefono">{{ $dom->cliente_telefono }}</td>
                                        <td data-label="Direccion">{{ $dom->cliente_direccion }}</td>
                                        <td data-label="Factura">
                                            <span class="badge bg-{{ $factura->estaActiva() ? 'success' : ($factura->estaAnulada() ? 'danger' : 'warning') }}">
                                                {{ $factura->numero_factura }}
                                            </span>
                                        </td>
                                        <td data-label="Total"><strong>${{ number_format($factura->valor_pagado, 0, ',', '.') }}</strong></td>
                                        <td data-label="Medio Pago">
                                            @if($factura->medio_pago === 'Efectivo')
                                                <span class="badge bg-success">Efectivo</span>
                                            @elseif($factura->medio_pago === 'Transferencia')
                                                <span class="badge bg-primary">Transferencia</span>
                                            @else
                                                <span class="badge bg-info">Mixto</span>
                                            @endif
                                        </td>
                                        <td data-label="Fecha">{{ $factura->fecha_hora_factura }}</td>
                                        <td data-label="Acciones">
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('factura.visual', $factura->numero_factura) }}" class="btn-primary-custom btn-sm" title="Imprimir" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <a href="{{ route('admin.factura.detalle', $factura->id) }}" class="btn-secondary-custom btn-sm" title="Detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Domicilios Cancelados --}}
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
        <h2><i class="bi bi-x-circle"></i> Cancelados ({{ $cancelados->count() }})</h2>
    </div>
    <div class="card-body-custom">
        @if($cancelados->isEmpty())
            <p class="text-muted text-center py-3">No hay domicilios cancelados en este periodo.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Telefono</th>
                            <th>Direccion</th>
                            <th>Fecha Cancelacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancelados as $dom)
                            <tr>
                                <td data-label="Cliente">{{ $dom->cliente_nombre }}</td>
                                <td data-label="Telefono">{{ $dom->cliente_telefono }}</td>
                                <td data-label="Direccion">{{ $dom->cliente_direccion }}</td>
                                <td data-label="Fecha Cancelacion">{{ $dom->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
