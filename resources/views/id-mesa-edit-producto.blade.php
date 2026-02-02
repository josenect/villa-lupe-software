@extends('layouts.app')

@section('title', 'Editar Producto - ' . $mesa->name)

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
        <i class="bi bi-pencil-square"></i> Editar Producto
    </h1>
    <a href="{{ route('mesa.show', ['id' => $mesa->id]) }}" class="btn-secondary-custom">
        <i class="bi bi-arrow-left"></i> Volver a la Mesa
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

<div class="row g-4">
    <!-- Info de la Mesa -->
    <div class="col-md-4">
        <div class="card-custom h-100 fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-info-circle"></i> Información</h2>
            </div>
            <div class="card-body-custom">
                <div class="mb-3">
                    <span class="text-muted">Mesa</span>
                    <h4 class="mb-0">{{ $mesa->name }}</h4>
                </div>
                <div class="mb-3">
                    <span class="text-muted">Ubicación</span>
                    <p class="mb-0"><i class="bi bi-geo-alt text-primary"></i> {{ $mesa->location }}</p>
                </div>
                <div>
                    <span class="text-muted">Estado</span>
                    <p class="mb-0">
                        <span class="status-badge {{ $mesa->status == 'Ocupada' ? 'ocupada' : 'disponible' }}">
                            {{ $mesa->status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Formulario de Edición -->
    <div class="col-md-8">
        <div class="card-custom fade-in">
            <div class="card-header-custom">
                <h2><i class="bi bi-pencil"></i> Editar Producto en Mesa {{ $mesa->name }}</h2>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('update.product.table', ['mesa_id' => $mesa->id, 'id' => $producto->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-box-seam"></i> Producto
                        </label>
                        <select id="product_id" name="producto_id" class="form-select-custom">
                            @foreach ($productos as $p)
                                <option value="{{ $p->id }}" {{ $p->id === $producto->producto_id ? 'selected' : '' }}>
                                    {{ $p->name }} - $ {{ number_format($p->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">
                                    <i class="bi bi-hash"></i> Cantidad
                                </label>
                                <input type="number" name="amount" class="form-control-custom" value="{{ old('amount', $producto->amount) }}" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">
                                    <i class="bi bi-percent"></i> Descuento por Unidad
                                </label>
                                <input type="number" name="dicount" class="form-control-custom" value="{{ old('dicount', $producto->dicount) }}" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="bi bi-chat-dots"></i> Observación
                        </label>
                        <input type="text" name="observacion" class="form-control-custom" value="{{ old('observacion', $producto->observacion) }}" placeholder="Ej: Sin cebolla, término medio...">
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-success-custom">
                            <i class="bi bi-check-lg"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('mesa.show', ['id' => $mesa->id]) }}" class="btn-secondary-custom">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
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
</script>
@endsection
