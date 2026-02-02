@extends('layouts.app')

@section('title', 'Cancelaciones Pendientes - Villa Lupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-x-circle"></i> Cancelaciones Pendientes
    </h1>
    <a href="/" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Inicio
    </a>
</div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-custom alert-error-custom fade-in">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="card-custom fade-in">
    <div class="card-header-custom" style="background: linear-gradient(135deg, #e74c3c, #c0392b) !important;">
        <h2><i class="bi bi-hourglass-split"></i> Solicitudes de Cancelación ({{ $cancelaciones->count() }})</h2>
    </div>
    <div class="card-body-custom">
        @if($cancelaciones->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-table"></i> Mesa</th>
                            <th><i class="bi bi-box"></i> Producto</th>
                            <th><i class="bi bi-hash"></i> Cant.</th>
                            <th><i class="bi bi-cash"></i> Valor</th>
                            <th><i class="bi bi-chat-text"></i> Motivo</th>
                            <th><i class="bi bi-person"></i> Solicitó</th>
                            <th><i class="bi bi-clock"></i> Hora</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cancelaciones as $item)
                            <tr>
                                <td data-label="Mesa">
                                    <span class="badge bg-primary">{{ $item->mesa->name ?? 'N/A' }}</span>
                                </td>
                                <td data-label="Producto"><strong>{{ $item->producto->name }}</strong></td>
                                <td data-label="Cantidad">
                                    <span class="badge bg-secondary">{{ $item->amount }}</span>
                                </td>
                                <td data-label="Valor">$ {{ number_format(($item->price - $item->dicount) * $item->amount, 0, ',', '.') }}</td>
                                <td data-label="Motivo">
                                    <span class="text-danger">{{ $item->motivo_cancelacion }}</span>
                                </td>
                                <td data-label="Solicitó">
                                    {{ $item->solicitadoPor->name ?? 'N/A' }}
                                </td>
                                <td data-label="Hora">
                                    <small>{{ $item->fecha_solicitud_cancelacion ? $item->fecha_solicitud_cancelacion->format('H:i') : '-' }}</small>
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        <form action="{{ route('admin.cancelacion.aprobar', $item->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-success-custom btn-sm-custom" title="Aprobar" onclick="return confirm('¿Aprobar la cancelación de {{ $item->producto->name }}?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.cancelacion.rechazar', $item->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-danger-custom btn-sm-custom" title="Rechazar" onclick="return confirm('¿Rechazar la cancelación?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h3 class="text-success mt-3">Sin solicitudes pendientes</h3>
                <p class="text-muted">No hay cancelaciones esperando aprobación</p>
            </div>
        @endif
    </div>
</div>
@endsection
