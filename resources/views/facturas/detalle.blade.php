@extends('layouts.app')

@section('title', 'Detalle Factura - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-receipt"></i> Factura {{ $factura->numero_factura }}
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.factura.showAll', date('Y-m-d', strtotime($factura->created_at))) }}" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        @if($factura->estado === 'activa')
            <a href="{{ route('admin.factura.showAnular', $factura->id) }}" class="btn-danger-custom">
                <i class="bi bi-x-circle"></i> Anular
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Detalle de la Factura -->
        <div class="card-custom mb-4 fade-in">
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="bi bi-file-text"></i> Detalle de Factura</h2>
                    @if($factura->estado === 'activa')
                        <span class="badge bg-success fs-6">Activa</span>
                    @elseif($factura->estado === 'anulada')
                        <span class="badge bg-danger fs-6">Anulada</span>
                    @else
                        <span class="badge bg-warning fs-6">Reabierta</span>
                    @endif
                </div>
            </div>
            <div class="card-body-custom">
                @if($factura->estado !== 'activa')
                    <div class="alert alert-danger mb-4">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <strong>Factura {{ $factura->estado }}:</strong> {{ $factura->motivo_anulacion ?? 'Sin motivo' }}
                        <br><small>Fecha: {{ $factura->fecha_anulacion }}</small>
                    </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Descuento</th>
                                <th>Subtotal</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->detalleFacturas as $detalle)
                                <tr>
                                    <td><strong>{{ $detalle->producto->name ?? 'Producto eliminado' }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ $detalle->amount }}</span></td>
                                    <td>$ {{ number_format($detalle->price, 0, ',', '.') }}</td>
                                    <td class="text-danger">- $ {{ number_format($detalle->discount, 0, ',', '.') }}</td>
                                    <td>$ {{ number_format($detalle->price * $detalle->amount, 0, ',', '.') }}</td>
                                    <td><strong>$ {{ number_format(($detalle->price - $detalle->discount) * $detalle->amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-end"><strong>SUBTOTAL:</strong></td>
                                <td colspan="2">$ {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="4" class="text-end"><strong>DESCUENTO TOTAL:</strong></td>
                                <td colspan="2">- $ {{ number_format($descuentoTotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                <td colspan="2"><strong>$ {{ number_format($total, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="4" class="text-end"><strong>PROPINA:</strong></td>
                                <td colspan="2">$ {{ number_format($factura->valor_propina, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row" style="background: linear-gradient(135deg, #27ae60, #1e8449);">
                                <td colspan="4" class="text-end"><strong>TOTAL PAGADO:</strong></td>
                                <td colspan="2"><strong class="fs-5">$ {{ number_format($factura->valor_pagado, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Info de la Factura -->
        <div class="card-custom mb-4 fade-in">
            <div class="card-body-custom">
                <h5 class="section-title"><i class="bi bi-info-circle"></i> Informacion</h5>
                
                <div class="mb-3">
                    <span class="text-muted">Numero de Factura</span>
                    <h4 class="mb-0 text-primary">{{ $factura->numero_factura }}</h4>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted">Mesa</span>
                    <p class="mb-0"><i class="bi bi-table text-primary"></i> {{ $factura->mesa->name ?? 'N/A' }}</p>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted">Fecha y Hora</span>
                    <p class="mb-0"><i class="bi bi-calendar text-primary"></i> {{ $factura->fecha_hora_factura }}</p>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted">Medio de Pago</span>
                    <p class="mb-0"><i class="bi bi-credit-card text-primary"></i> {{ $factura->medio_pago }}</p>
                </div>
                
                <div>
                    <span class="text-muted">Estado</span>
                    <p class="mb-0">
                        @if($factura->estado === 'activa')
                            <span class="status-badge activo"><i class="bi bi-check-circle"></i> Activa</span>
                        @elseif($factura->estado === 'anulada')
                            <span class="status-badge" style="background-color: rgba(231, 76, 60, 0.15); color: #e74c3c;">
                                <i class="bi bi-x-circle"></i> Anulada
                            </span>
                        @else
                            <span class="status-badge" style="background-color: rgba(243, 156, 18, 0.15); color: #f39c12;">
                                <i class="bi bi-arrow-counterclockwise"></i> Reabierta
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="card-custom fade-in">
            <div class="card-body-custom text-center">
                <h5 class="text-muted mb-3">Total Pagado</h5>
                <h2 class="text-success mb-0" style="font-size: 2.5rem;">$ {{ number_format($factura->valor_pagado, 0, ',', '.') }}</h2>
                <small class="text-muted">(Incluye propina de $ {{ number_format($factura->valor_propina, 0, ',', '.') }})</small>
            </div>
        </div>
    </div>
</div>
@endsection
