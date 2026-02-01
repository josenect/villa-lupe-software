@extends('layouts.app')

@section('title', 'Anular Factura - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-x-circle"></i> Anular Factura
    </h1>
    <a href="{{ route('admin.factura.showAll', date('Y-m-d')) }}" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Info de la Factura -->
        <div class="card-custom mb-4 fade-in">
            <div class="card-header-custom bg-danger" style="background: linear-gradient(135deg, #e74c3c, #c0392b) !important;">
                <h2><i class="bi bi-exclamation-triangle"></i> Factura a Anular: {{ $factura->numero_factura }}</h2>
            </div>
            <div class="card-body-custom">
                <div class="alert alert-warning mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Advertencia:</strong> Esta accion anulara la factura y no se contabilizara en los reportes.
                    Si desea cargar los productos nuevamente a la mesa, use la opcion "Reabrir Factura".
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-receipt"></i> Numero:</strong> {{ $factura->numero_factura }}</p>
                        <p><strong><i class="bi bi-table"></i> Mesa:</strong> {{ $factura->mesa->name ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-calendar"></i> Fecha:</strong> {{ $factura->fecha_hora_factura }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-cash"></i> Subtotal:</strong> $ {{ number_format($factura->valor_total, 0, ',', '.') }}</p>
                        <p><strong><i class="bi bi-heart"></i> Propina:</strong> $ {{ number_format($factura->valor_propina, 0, ',', '.') }}</p>
                        <p><strong><i class="bi bi-cash-stack"></i> Total Pagado:</strong> <span class="text-success fs-5">$ {{ number_format($factura->valor_pagado, 0, ',', '.') }}</span></p>
                    </div>
                </div>
                
                <!-- Productos de la factura -->
                <h5 class="section-title"><i class="bi bi-box"></i> Productos en la Factura</h5>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Descuento</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->detalleFacturas as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->name ?? 'Producto eliminado' }}</td>
                                    <td><span class="badge bg-secondary">{{ $detalle->amount }}</span></td>
                                    <td>$ {{ number_format($detalle->price, 0, ',', '.') }}</td>
                                    <td>$ {{ number_format($detalle->discount, 0, ',', '.') }}</td>
                                    <td><strong>$ {{ number_format(($detalle->price - $detalle->discount) * $detalle->amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Formulario de Anulacion -->
        <div class="card-custom fade-in">
            <div class="card-body-custom">
                <h5 class="section-title"><i class="bi bi-chat-text"></i> Motivo de Anulacion</h5>
                
                <div class="row">
                    <!-- Opcion 1: Solo Anular -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border: 2px solid #e74c3c;">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Solo Anular</h5>
                                <p class="text-muted small">La factura se anula y los productos NO se cargan a la mesa.</p>
                                <form action="{{ route('admin.factura.anular', $factura->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <textarea name="motivo" class="form-control-custom" rows="2" placeholder="Motivo de anulacion (opcional)"></textarea>
                                    </div>
                                    <button type="submit" class="btn-danger-custom w-100 justify-content-center" onclick="return confirm('¿Esta seguro de ANULAR esta factura?')">
                                        <i class="bi bi-x-lg"></i> Anular Factura
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opcion 2: Reabrir (Anular y cargar productos) -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border: 2px solid #f39c12;">
                            <div class="card-body text-center">
                                <i class="bi bi-arrow-counterclockwise text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Reabrir Factura</h5>
                                <p class="text-muted small">La factura se anula y los productos se cargan nuevamente a la mesa.</p>
                                <form action="{{ route('admin.factura.reabrir', $factura->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <textarea name="motivo" class="form-control-custom" rows="2" placeholder="Motivo de reapertura (opcional)"></textarea>
                                    </div>
                                    <button type="submit" class="btn-warning-custom w-100 justify-content-center" onclick="return confirm('¿Esta seguro de REABRIR esta factura? Los productos se cargaran a la mesa.')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reabrir Factura
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
