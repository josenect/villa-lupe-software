@extends('layouts.app')

@section('title', 'Pendientes por Cobrar')

@section('styles')
<style>
    .print-only { display: none; }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .fade-in { animation: none !important; }
        body { font-size: 12px; }
        .card-custom { box-shadow: none !important; border: 1px solid #ddd !important; }
        .card-header-custom { background: #f0f0f0 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
        .total-row { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $restNombre = \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h1 class="mb-0"><i class="bi bi-hourglass-split text-warning"></i> Pendientes por Cobrar</h1>
        <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i:s') }}</small>
    </div>
    <a href="{{ route('admin.pendientes.imprimir') }}" target="_blank" class="btn-primary-custom no-print">
        <i class="bi bi-printer"></i> Imprimir
    </a>
</div>

{{-- Encabezado solo para impresión --}}
<div class="print-only text-center mb-3">
    <strong style="font-size:1.1rem;">{{ strtoupper($restNombre) }}</strong><br>
    <span>Pendientes por Cobrar — {{ now()->format('d/m/Y H:i') }}</span>
</div>

{{-- Tarjetas resumen --}}
<div class="row g-3 mb-4 fade-in">
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div class="text-muted small mb-1"><i class="bi bi-grid-3x3-gap"></i> Mesas activas</div>
            <div class="fw-bold fs-3 text-primary">{{ $mesas->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div class="text-muted small mb-1"><i class="bi bi-cash-stack"></i> Subtotal</div>
            <div class="fw-bold fs-5 text-dark">$ {{ number_format($totalGlobal, 0, ',', '.') }}</div>
        </div>
    </div>
    @if($propinaHabilitada)
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div class="text-muted small mb-1"><i class="bi bi-heart text-danger"></i> Propina ({{ $propinaPct }}%)</div>
            <div class="fw-bold fs-5 text-danger">$ {{ number_format($propinaGlobal, 0, ',', '.') }}</div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div class="text-muted small mb-1"><i class="bi bi-wallet2 text-success"></i> Gran Total</div>
            <div class="fw-bold fs-4 text-success">$ {{ number_format($granTotal, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

@if($mesas->isEmpty())
    <div class="card-custom text-center py-5 fade-in">
        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
        <h4 class="mt-3 text-muted">No hay mesas con saldo pendiente</h4>
    </div>
@else
{{-- Tabla de mesas --}}
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-table"></i> Detalle por Mesa</h2>
    </div>
    <div class="card-body-custom">
        <div style="overflow-x: auto;">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th><i class="bi bi-grid-3x3-gap"></i> Mesa</th>
                        <th><i class="bi bi-geo-alt"></i> Ubicación</th>
                        <th><i class="bi bi-clock-history"></i> Tiempo</th>
                        <th class="text-end"><i class="bi bi-receipt"></i> Subtotal</th>
                        @if($propinaHabilitada)
                        <th class="text-end"><i class="bi bi-heart"></i> Propina</th>
                        @endif
                        <th class="text-end"><i class="bi bi-wallet2"></i> Total</th>
                        <th class="text-center no-print"><i class="bi bi-eye"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mesas as $mesa)
                    <tr>
                        <td><strong>{{ $mesa->name }}</strong></td>
                        <td><small class="text-muted">{{ $mesa->location }}</small></td>
                        <td>
                            @if($mesa->occupied_at)
                                <span class="badge bg-secondary tiempo-mesa" data-since="{{ $mesa->occupied_at->toIso8601String() }}">—</span>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td class="text-end">$ {{ number_format($mesa->subtotal_pendiente, 0, ',', '.') }}</td>
                        @if($propinaHabilitada)
                        <td class="text-end text-danger">$ {{ number_format($mesa->propina_pendiente, 0, ',', '.') }}</td>
                        @endif
                        <td class="text-end"><strong class="text-success">$ {{ number_format($mesa->total_pendiente, 0, ',', '.') }}</strong></td>
                        <td class="text-center no-print">
                            <a href="/mesa/{{ $mesa->id }}" class="btn-primary-custom btn-sm-custom" title="Ver mesa">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    {{-- Detalle de productos de la mesa --}}
                    <tr class="bg-light">
                        <td colspan="{{ $propinaHabilitada ? 7 : 6 }}" class="py-2 px-4">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($mesa->elementTables as $item)
                                <span class="badge bg-white border text-dark" style="font-weight:400; font-size:0.78rem;">
                                    {{ $item->amount }}× {{ $item->producto->name }}
                                    <span class="text-muted">($&nbsp;{{ number_format(($item->price - $item->dicount) * $item->amount, 0, ',', '.') }})</span>
                                </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3"><strong>TOTALES</strong></td>
                        <td class="text-end"><strong>$ {{ number_format($totalGlobal, 0, ',', '.') }}</strong></td>
                        @if($propinaHabilitada)
                        <td class="text-end text-danger"><strong>$ {{ number_format($propinaGlobal, 0, ',', '.') }}</strong></td>
                        @endif
                        <td class="text-end text-success"><strong>$ {{ number_format($granTotal, 0, ',', '.') }}</strong></td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function tiempoTranscurrido(since) {
    const diff = Math.floor((Date.now() - new Date(since)) / 1000);
    if (diff < 60) return diff + 's';
    const h = Math.floor(diff / 3600), m = Math.floor((diff % 3600) / 60);
    return h > 0 ? h + 'h ' + m + 'm' : m + 'm';
}
function actualizarTiempos() {
    document.querySelectorAll('.tiempo-mesa[data-since]').forEach(function(el) {
        el.textContent = tiempoTranscurrido(el.dataset.since);
    });
}
actualizarTiempos();
setInterval(actualizarTiempos, 30000);
</script>
@endsection
