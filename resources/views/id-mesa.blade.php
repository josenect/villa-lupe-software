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
    .pago-box {
        padding: 10px;
        border-radius: 10px;
        background: #f8f9fa;
    }
    .pago-box.efectivo {
        border: 2px solid #27ae60;
    }
    .pago-box.transferencia {
        border: 2px solid #3498db;
    }
    .pago-box .input-group-text {
        background: white;
        border-color: #dee2e6;
    }
    .input-group-text {
        background: #e9ecef;
        border: 2px solid #e9ecef;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }
    
    /* Responsive para formulario agregar producto */
    @media (max-width: 768px) {
        .form-agregar-producto .row {
            gap: 0.5rem;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--single {
            height: 52px;
            font-size: 16px;
        }
        
        /* Botones de acción en la mesa */
        .action-buttons-mesa {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }
        
        .action-buttons-mesa a,
        .action-buttons-mesa button {
            width: 100%;
            justify-content: center;
        }
        
        /* Modal responsive */
        .modal-body .pago-box {
            margin-bottom: 0.5rem;
        }
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

@if(session('error'))
    <div class="alert-custom alert-error-custom fade-in">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
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
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="action-buttons action-buttons-mesa justify-content-md-end">
                            @if($productosTable->count() > 0)
                                <a target="_blank" href="/visual-pdf-pre/{{ $mesa->id }}" class="btn-warning-custom">
                                    <i class="bi bi-printer"></i> Preliminar
                                </a>
                                @if(auth()->user() && auth()->user()->esAdmin())
                                    <a href="#" id="generar-factura-btn" data-mesa-id="{{ $mesa->id }}" class="btn-success-custom">
                                        <i class="bi bi-receipt"></i> Facturar
                                    </a>
                                @endif
                            @else
                                @if(auth()->user() && auth()->user()->esAdmin())
                                    <a target="_blank" href="/generar-factura/{{ $mesa->id }}" class="btn-primary-custom">
                                        <i class="bi bi-eye"></i> Ver Última
                                    </a>
                                @endif
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

<!-- Agregar Producto -->
<div class="card-custom mb-4 fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-plus-circle"></i> Agregar Producto</h2>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('add.product.table', ['mesa_id' => $mesa->id]) }}" method="POST" class="form-agregar-producto">
            @csrf
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-box-seam"></i> Producto
                        </label>
                        <select id="product_id" name="product_id" class="form-select-custom" required>
                            <option value="">Seleccionar Producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}">{{ $producto->name }} - $ {{ number_format($producto->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-hash"></i> Cant.
                        </label>
                        <input type="number" name="amount" class="form-control-custom" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-percent"></i> Desc.
                        </label>
                        <input type="number" name="dicount" class="form-control-custom" min="0" value="0">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">
                            <i class="bi bi-chat-dots"></i> Nota
                        </label>
                        <input type="text" name="observacion" class="form-control-custom" placeholder="Sin cebolla, término medio...">
                    </div>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-success-custom w-100 justify-content-center" style="min-height: 48px;">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Generar Factura -->
<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #1e8449); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title" id="modalFacturaLabel">
                    <i class="bi bi-receipt"></i> Generar Factura
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <h6 class="text-muted mb-2">Total de la Mesa</h6>
                    <h2 class="text-primary mb-0">$ {{ number_format($total, 0, ',', '.') }}</h2>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i class="bi bi-heart text-danger"></i> Propina
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="modal-propina" class="form-control-custom" value="{{ number_format(($total * env('PROPINA'))/100, 0, '', '') }}" min="0" style="border-radius: 0 10px 10px 0;">
                    </div>
                    <small class="text-muted">Sugerida ({{ env('PROPINA') }}%): $ {{ number_format(($total * env('PROPINA'))/100, 0, ',', '.') }}</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i class="bi bi-credit-card"></i> Forma de Pago
                    </label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="pago-box efectivo">
                                <label class="small text-success mb-1"><i class="bi bi-cash-stack"></i> Efectivo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="modal-efectivo" class="form-control" value="{{ $total + number_format(($total * env('PROPINA'))/100, 0, '', '') }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pago-box transferencia">
                                <label class="small text-primary mb-1"><i class="bi bi-phone"></i> Transferencia</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="modal-transferencia" class="form-control" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">Puede dividir el pago entre efectivo y transferencia</small>
                </div>
                
                <div class="bg-light p-3 rounded-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>$ {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Propina:</span>
                        <span id="modal-propina-display">$ {{ number_format(($total * env('PROPINA'))/100, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Total a Pagar:</strong>
                        <strong class="text-success" id="modal-total-display">$ {{ number_format($total + (($total * env('PROPINA'))/100), 0, ',', '.') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-success"><i class="bi bi-cash-stack"></i> Efectivo:</span>
                        <span id="resumen-efectivo">$ {{ number_format($total + (($total * env('PROPINA'))/100), 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-primary"><i class="bi bi-phone"></i> Transferencia:</span>
                        <span id="resumen-transferencia">$ 0</span>
                    </div>
                </div>
                
                <div id="alerta-pago" class="alert alert-danger mt-3 mb-0 d-none" style="font-size: 0.85rem;">
                    <i class="bi bi-exclamation-triangle"></i> La suma de efectivo y transferencia debe ser igual al total a pagar.
                </div>
            </div>
            <div class="modal-footer" style="border: none;">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button type="button" class="btn-success-custom" id="confirmar-factura-btn">
                    <i class="bi bi-check-lg"></i> Confirmar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Productos en la Mesa -->
<div class="card-custom fade-in">
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
                            <th><i class="bi bi-person"></i> Mesero</th>
                            <th><i class="bi bi-hash"></i> Cant.</th>
                            <th><i class="bi bi-cash-stack"></i> Total</th>
                            <th><i class="bi bi-chat-dots"></i> Obs.</th>
                            <th><i class="bi bi-flag"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productosTable as $producto)
                            <tr class="{{ $producto->estado === 'cancelacion_solicitada' ? 'table-warning' : '' }}">
                                <td data-label="Producto"><strong>{{ $producto->producto->name }}</strong></td>
                                <td data-label="Mesero">
                                    <small class="text-primary">{{ $producto->usuario->name ?? 'N/A' }}</small>
                                </td>
                                <td data-label="Cantidad">
                                    <span class="badge bg-secondary">{{ $producto->amount }}</span>
                                </td>
                                <td data-label="Total"><strong>$ {{ number_format(($producto->price - $producto->dicount) * $producto->amount, 0, ',', '.') }}</strong></td>
                                <td data-label="Obs.">
                                    @if($producto->observacion)
                                        <small class="text-info"><i class="bi bi-chat-dots"></i> {{ $producto->observacion }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    @switch($producto->estado)
                                        @case('pendiente')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pendiente</span>
                                            @break
                                        @case('en_cocina')
                                            <span class="badge bg-info"><i class="bi bi-fire"></i> En Cocina</span>
                                            @break
                                        @case('listo')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Listo</span>
                                            @break
                                        @case('entregado')
                                            <span class="badge bg-primary"><i class="bi bi-check2-all"></i> Entregado</span>
                                            @break
                                        @case('cancelacion_solicitada')
                                            <span class="badge bg-danger"><i class="bi bi-hourglass-split"></i> Cancel. Solicitada</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $producto->estado }}</span>
                                    @endswitch
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        @if($producto->estado !== 'cancelacion_solicitada')
                                            <a class="btn-primary-custom btn-sm-custom" href="{{ route('show.product.table', ['mesa_id' => $mesa->id, 'id' => $producto->id]) }}" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(auth()->user() && auth()->user()->esAdmin())
                                                <a class="btn-danger-custom btn-sm-custom" href="{{ route('delete.product.table', ['mesa_id' => $mesa->id, 'id' => $producto->id]) }}" onclick="return confirm('¿Está seguro de eliminar este producto?')" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn-warning-custom btn-sm-custom btn-cancelar" data-id="{{ $producto->id }}" data-nombre="{{ $producto->producto->name }}" title="Solicitar Cancelación">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @endif
                                        @else
                                            <small class="text-muted">Esperando...</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong>$ {{ number_format($total, 0, ',', '.') }}</strong></td>
                            <td colspan="3">
                                <small>Propina sugerida: $ {{ number_format(($total * env('PROPINA'))/100, 0, ',', '.') }}</small>
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

<!-- Modal Solicitar Cancelación -->
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="bi bi-x-circle"></i> Solicitar Cancelación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCancelar" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p>¿Desea solicitar la cancelación de: <strong id="nombreProductoCancelar"></strong>?</p>
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-chat-text"></i> Motivo de cancelación</label>
                        <select name="motivo" class="form-select-custom" required>
                            <option value="">Seleccione un motivo</option>
                            <option value="Cliente ya no lo quiere">Cliente ya no lo quiere</option>
                            <option value="Se demoró mucho">Se demoró mucho</option>
                            <option value="Error al pedir">Error al pedir</option>
                            <option value="Se dañó en cocina">Se dañó en cocina</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border: none;">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-danger-custom">
                        <i class="bi bi-send"></i> Enviar Solicitud
                    </button>
                </div>
            </form>
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
    
    var totalMesa = {{ $total }};
    var propinaSugerida = {{ number_format(($total * env('PROPINA'))/100, 0, '', '') }};
    
    function actualizarTotales() {
        var propina = parseFloat(document.getElementById('modal-propina').value) || 0;
        var totalPagar = totalMesa + propina;
        var efectivo = parseFloat(document.getElementById('modal-efectivo').value) || 0;
        var transferencia = parseFloat(document.getElementById('modal-transferencia').value) || 0;
        var sumaPagos = efectivo + transferencia;
        
        // Actualizar displays
        document.getElementById('modal-propina-display').textContent = '$ ' + propina.toLocaleString('es-CO');
        document.getElementById('modal-total-display').textContent = '$ ' + totalPagar.toLocaleString('es-CO');
        document.getElementById('resumen-efectivo').textContent = '$ ' + efectivo.toLocaleString('es-CO');
        document.getElementById('resumen-transferencia').textContent = '$ ' + transferencia.toLocaleString('es-CO');
        
        // Validar que la suma sea igual al total
        var alerta = document.getElementById('alerta-pago');
        var btnConfirmar = document.getElementById('confirmar-factura-btn');
        
        if (Math.abs(sumaPagos - totalPagar) > 0.01) {
            alerta.classList.remove('d-none');
            btnConfirmar.disabled = true;
            btnConfirmar.style.opacity = '0.5';
        } else {
            alerta.classList.add('d-none');
            btnConfirmar.disabled = false;
            btnConfirmar.style.opacity = '1';
        }
    }
    
    // Abrir modal al hacer clic en Generar Factura
    document.getElementById('generar-factura-btn')?.addEventListener('click', function(event) {
        event.preventDefault();
        // Resetear valores al abrir
        var totalPagar = totalMesa + propinaSugerida;
        document.getElementById('modal-propina').value = propinaSugerida;
        document.getElementById('modal-efectivo').value = totalPagar;
        document.getElementById('modal-transferencia').value = 0;
        actualizarTotales();
        var modal = new bootstrap.Modal(document.getElementById('modalFactura'));
        modal.show();
    });
    
    // Actualizar cuando cambia la propina
    document.getElementById('modal-propina')?.addEventListener('input', function() {
        var propina = parseFloat(this.value) || 0;
        var totalPagar = totalMesa + propina;
        // Auto-ajustar efectivo si transferencia es 0
        var transferencia = parseFloat(document.getElementById('modal-transferencia').value) || 0;
        if (transferencia === 0) {
            document.getElementById('modal-efectivo').value = totalPagar;
        }
        actualizarTotales();
    });
    
    // Actualizar cuando cambia efectivo
    document.getElementById('modal-efectivo')?.addEventListener('input', actualizarTotales);
    
    // Actualizar cuando cambia transferencia
    document.getElementById('modal-transferencia')?.addEventListener('input', actualizarTotales);
    
    // Confirmar y generar factura
    document.getElementById('confirmar-factura-btn')?.addEventListener('click', function() {
        var mesaId = document.getElementById('generar-factura-btn').getAttribute('data-mesa-id');
        var propina = document.getElementById('modal-propina').value || 0;
        var efectivo = document.getElementById('modal-efectivo').value || 0;
        var transferencia = document.getElementById('modal-transferencia').value || 0;
        
        // Determinar método de pago
        var metodoPago = 'Efectivo';
        if (parseFloat(efectivo) > 0 && parseFloat(transferencia) > 0) {
            metodoPago = 'Mixto';
        } else if (parseFloat(transferencia) > 0) {
            metodoPago = 'Transferencia';
        }
        
        var url = '/generar-factura/' + mesaId + '?propina=' + propina + '&efectivo=' + efectivo + '&transferencia=' + transferencia + '&medio_pago=' + encodeURIComponent(metodoPago);
        window.location.href = url;
    });
    
    // Modal de cancelación
    document.querySelectorAll('.btn-cancelar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var nombre = this.getAttribute('data-nombre');
            document.getElementById('nombreProductoCancelar').textContent = nombre;
            document.getElementById('formCancelar').action = '/mesa/{{ $mesa->id }}/productos/' + id + '/solicitar-cancelacion';
            var modal = new bootstrap.Modal(document.getElementById('modalCancelar'));
            modal.show();
        });
    });
</script>
@endsection
