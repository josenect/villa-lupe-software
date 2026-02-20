@extends('layouts.app')

@section('title', 'Historial de Cancelaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-clock-history"></i> Historial de Cancelaciones
    </h1>
    <a href="{{ route('admin.cancelaciones.pendientes') }}" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Pendientes
    </a>
</div>

{{-- Filtro de fechas --}}
<div class="card-custom mb-4 fade-in">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('admin.cancelaciones.historial') }}" class="d-flex flex-wrap gap-3 align-items-end">
            <div>
                <label class="form-label-custom" style="font-size:0.8rem;margin-bottom:4px;">
                    <i class="bi bi-calendar3"></i> Desde
                </label>
                <input type="date" name="desde" value="{{ $desde }}" class="form-control-custom" style="width:auto;">
            </div>
            <div>
                <label class="form-label-custom" style="font-size:0.8rem;margin-bottom:4px;">
                    <i class="bi bi-calendar3"></i> Hasta
                </label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="form-control-custom" style="width:auto;">
            </div>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-search"></i> Buscar
            </button>
            @if($historial->count() > 0)
            <a href="{{ route('admin.cancelaciones.exportarCSV', ['desde' => $desde, 'hasta' => $hasta]) }}"
               class="btn-success-custom">
                <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Métricas --}}
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-2">
                    <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
                </div>
                <h3 class="text-success mb-1">{{ $aprobadas }}</h3>
                <p class="text-muted mb-0">Cancelaciones Aprobadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-2">
                    <i class="bi bi-x-circle text-warning" style="font-size:2rem;"></i>
                </div>
                <h3 class="text-warning mb-1">{{ $rechazadas }}</h3>
                <p class="text-muted mb-0">Solicitudes Rechazadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-2">
                    <i class="bi bi-currency-dollar text-danger" style="font-size:2rem;"></i>
                </div>
                <h3 class="text-danger mb-1">
                    $ {{ number_format($valorTotal, 0, ',', '.') }}
                </h3>
                <p class="text-muted mb-0">Valor Total Cancelado</p>
            </div>
        </div>
    </div>
</div>

{{-- Tabla historial --}}
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2>
            <i class="bi bi-list-ul"></i> Registros
            <small class="opacity-75 ms-2" style="font-size:0.85rem;">
                {{ $desde === $hasta ? $desde : $desde . ' → ' . $hasta }}
            </small>
        </h2>
    </div>
    <div class="card-body-custom">
        @if($historial->count() > 0)
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Mesa</th>
                        <th>Cant.</th>
                        <th>Valor</th>
                        <th>Motivo</th>
                        <th>Solicitado por</th>
                        <th>Fecha solicitud</th>
                        <th>Estado</th>
                        <th>Gestionado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $item)
                    @php
                        $esAprobada = $item->estado === \App\Models\ElementTable::ESTADO_CANCELADO;
                    @endphp
                    <tr>
                        <td data-label="Producto"><strong>{{ $item->producto->name ?? '—' }}</strong></td>
                        <td data-label="Mesa">{{ $item->mesa->name ?? '—' }}</td>
                        <td data-label="Cant.">{{ $item->amount }}</td>
                        <td data-label="Valor">
                            $ {{ number_format(($item->price - $item->dicount) * $item->amount, 0, ',', '.') }}
                        </td>
                        <td data-label="Motivo">
                            <small class="text-muted">{{ $item->motivo_cancelacion ?? '—' }}</small>
                        </td>
                        <td data-label="Solicitado por">
                            {{ $item->solicitadoPor->name ?? '—' }}
                        </td>
                        <td data-label="Fecha solicitud">
                            <small>{{ $item->fecha_solicitud_cancelacion ? \Carbon\Carbon::parse($item->fecha_solicitud_cancelacion)->format('d/m H:i') : '—' }}</small>
                        </td>
                        <td data-label="Estado">
                            @if($esAprobada)
                                <span class="status-badge disponible">
                                    <i class="bi bi-check-circle"></i> Aprobada
                                </span>
                            @else
                                <span class="status-badge inactivo">
                                    <i class="bi bi-x-circle"></i> Rechazada
                                </span>
                            @endif
                        </td>
                        <td data-label="Gestionado por">
                            {{ $item->aprobadoPor->name ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3">No hay cancelaciones en el período seleccionado</p>
            </div>
        @endif
    </div>
</div>
@endsection
