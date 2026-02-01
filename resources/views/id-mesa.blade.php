@extends('layouts.app')

@section('title', $mesa->name . ' - Villa Lupe')

@section('styles')
<link href="/bookstores/select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        height: 48px;
        padding: 8px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
    .select2-dropdown {
        border-radius: 10px;
        border: 2px solid #e9ecef;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3498db;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-table"></i> {{ $mesa->name }}
    </h1>
    <a href="/" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Info de la Mesa -->
<div class="row g-4 mb-4 fade-in">
    <div class="col-md-8">
        <div class="card-custom h-100">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="bi bi-info-circle"></i> Información de la Mesa</h2>
                <span class="status-badge {{ $mesa->status == 'Ocupada' ? 'ocupada' : 'disponible' }}" style="font-size: 1rem;">
                    {{ $mesa->status }}
                </span>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong><i class="bi bi-tag text-primary"></i> Nombre:</strong> {{ $mesa->name }}</p>
                        <p class="mb-0"><strong><i class="bi bi-geo-alt text-primary"></i> Ubicación:</strong> {{ $mesa->location }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="action-buttons justify-content-md-end">
                            @if($productosTable->count() > 0)
                                <a target="_blank" href="/visual-pdf-pre/{{ $mesa->id }}" class="btn-warning-custom">
                                    <i class="bi bi-file-earmark-pdf"></i> Factura Preliminar
                                </a>
                                <a href="#" id="generar-factura-btn" data-mesa-id="{{ $mesa->id }}" class="btn-success-custom">
                                    <i class="bi bi-receipt"></i> Generar Factura
                                </a>
                            @else
                                <a target="_blank" href="/generar-factura/{{ $mesa->id }}" class="btn-primary-custom">
                                    <i class="bi bi-eye"></i> Ver Última Factura
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card-custom h-100">
            <div class="card-body-custom text-center d-flex flex-column justify-content-center">
                <h5 class="text-muted mb-3">Total Actual</h5>
                <h2 class="text-primary mb-2" style="font-size: 2.5rem;">$ {{ number_format($total, 0, ',', '.') }}</h2>
                <p class="text-muted mb-0">
                    <small>Propina sugerida ({{ env('PROPINA') }}%): <strong>$ {{ number_format(($total * env('PROPINA'))/100, 0, ',', '.') }}</strong></small>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Productos en la Mesa -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-cart3"></i> Productos en la Mesa</h2>
    </div>
    <div class="card-body-custom">
        @if($productosTable->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-box"></i> Producto</th>
                            <th><i class="bi bi-hash"></i> Cantidad</th>
                            <th><i class="bi bi-currency-dollar"></i> Precio</th>
                            <th><i class="bi bi-percent"></i> Desc. Unit.</th>
                            <th><i class="bi bi-calculator"></i> Subtotal</th>
                            <th><i class="bi bi-dash-circle"></i> Descuento</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                            <th><i class="bi bi-calendar"></i> Fecha</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productosTable as $producto)
                            <tr>
                                <td><strong>{{ $producto->producto->name }}</strong></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $producto->amount }}</span>
                                </td>
                                <td>$ {{ number_format($producto->price, 0, ',', '.') }}</td>
                                <td>$ {{ number_format($producto->dicount, 0, ',', '.') }}</td>
                                <td>$ {{ number_format($producto->price * $producto->amount, 0, ',', '.') }}</td>
                                <td class="text-danger">- $ {{ number_format($producto->dicount * $producto->amount, 0, ',', '.') }}</td>
                                <td><strong>$ {{ number_format(($producto->price - $producto->dicount) * $producto->amount, 0, ',', '.') }}</strong></td>
                                <td>
                                    <small class="text-muted">{{ date('Y-m-d g:i A', strtotime($producto->record)) }}</small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-primary-custom btn-sm-custom" href="{{ route('show.product.table', ['mesa_id' => $mesa->id, 'id' => $producto->id]) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a class="btn-danger-custom btn-sm-custom" href="{{ route('delete.product.table', ['mesa_id' => $mesa->id, 'id' => $producto->id]) }}" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="4" class="text-end"><strong>TOTALES:</strong></td>
                            <td>$ {{ number_format($subtotal, 0, ',', '.') }}</td>
                            <td>- $ {{ number_format($descuentoTotal, 0, ',', '.') }}</td>
                            <td><strong>$ {{ number_format($total, 0, ',', '.') }}</strong></td>
                            <td colspan="2">
                                <small>Propina: $ {{ number_format(($total * env('PROPINA'))/100, 0, ',', '.') }}</small>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay productos agregados a esta mesa</p>
            </div>
        @endif
    </div>
</div>

<!-- Agregar Producto -->
<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-plus-circle"></i> Agregar Producto</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('add.product.table', ['mesa_id' => $mesa->id]) }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-box-seam"></i> Seleccione un producto
                        </label>
                        <select id="product_id" name="product_id" class="form-select-custom" required>
                            <option value="">Seleccionar Producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}">{{ $producto->name }} - $ {{ number_format($producto->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-hash"></i> Cantidad
                        </label>
                        <input type="number" name="amount" class="form-control-custom" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-percent"></i> Descuento
                        </label>
                        <input type="number" name="dicount" class="form-control-custom" min="0" value="0">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-success-custom w-100 justify-content-center">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="/bookstores/jquery/jquery-3.7.1.min.js.js"></script>
<script src="/bookstores/select2/dist/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product_id').select2({
            placeholder: "Buscar producto...",
            allowClear: true,
            width: '100%'
        });
    });
    
    document.getElementById('generar-factura-btn')?.addEventListener('click', function(event) {
        event.preventDefault();
        var mesaId = this.getAttribute('data-mesa-id');
        var propina = prompt('Ingrese el valor de la propina:', '0');
        if (propina !== null) {
            var url = '/generar-factura/' + mesaId + '?propina=' + propina;
            window.open(url, '_blank');
        }
    });
</script>
@endsection
