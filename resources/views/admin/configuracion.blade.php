@extends('layouts.app')

@section('title', 'Configuración — ' . \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe'))

@section('styles')
<style>
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }
    .toggle-switch input { display: none; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 28px;
        transition: 0.3s;
    }
    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        left: 4px;
        bottom: 4px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider { background: #27ae60; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }

    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    .toggle-row:last-child { border-bottom: none; }
    .toggle-label { font-weight: 500; color: #2c3e50; }
    .toggle-desc { font-size: 0.85rem; color: #888; margin-top: 2px; }

    .logo-preview {
        width: 90px;
        height: 90px;
        object-fit: contain;
        border-radius: 12px;
        border: 2px solid #eee;
        background: #f8f9fa;
        padding: 6px;
    }
    .logo-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 12px;
        border: 2px dashed #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #aaa;
        font-size: 2rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="page-title mb-0">
        <i class="bi bi-sliders"></i> Configuración
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

<form action="{{ route('admin.configuracion.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ── Información del Restaurante ─────────────────────────── --}}
    <div class="card-custom mb-4 fade-in">
        <div class="card-header-custom">
            <h2><i class="bi bi-shop"></i> Información del Restaurante</h2>
        </div>
        <div class="card-body-custom">
            <div class="row g-4">
                {{-- Logo --}}
                <div class="col-12 col-md-auto d-flex flex-column align-items-center gap-2">
                    @if($settings['restaurante_logo'])
                        <img src="{{ asset('storage/' . $settings['restaurante_logo']) }}"
                             alt="Logo" class="logo-preview" id="logoPreview">
                    @else
                        <div class="logo-placeholder" id="logoPlaceholder">
                            <i class="bi bi-image"></i>
                        </div>
                        <img src="" alt="Logo" class="logo-preview d-none" id="logoPreview">
                    @endif
                    <label class="btn-secondary-custom" style="cursor:pointer; font-size:0.85rem; padding:0.4rem 0.9rem;">
                        <i class="bi bi-upload"></i> Subir logo
                        <input type="file" name="restaurante_logo" id="logoInput" accept="image/*" class="d-none">
                    </label>
                    <small class="text-muted text-center" style="font-size:0.75rem;">PNG, JPG · máx 2MB</small>
                </div>

                {{-- Datos --}}
                <div class="col">
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-shop"></i> Nombre del Restaurante</label>
                        <input type="text" name="restaurante_nombre" class="form-control-custom"
                               value="{{ old('restaurante_nombre', $settings['restaurante_nombre']) }}" required>
                        @error('restaurante_nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label-custom"><i class="bi bi-building"></i> Propiedad / Subtítulo</label>
                        <input type="text" name="restaurante_propiedad" class="form-control-custom"
                               value="{{ old('restaurante_propiedad', $settings['restaurante_propiedad']) }}"
                               placeholder="Ej: Casa de Campo">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label-custom"><i class="bi bi-geo-alt"></i> Dirección</label>
                        <input type="text" name="restaurante_direccion" class="form-control-custom"
                               value="{{ old('restaurante_direccion', $settings['restaurante_direccion']) }}"
                               placeholder="Ej: Calle 10 # 5-20, Bogotá">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Propina ──────────────────────────────────────────────── --}}
    <div class="card-custom mb-4 fade-in">
        <div class="card-header-custom">
            <h2><i class="bi bi-heart"></i> Propina</h2>
        </div>
        <div class="card-body-custom">
            <div class="toggle-row">
                <div>
                    <div class="toggle-label"><i class="bi bi-toggle-on"></i> Habilitar propina</div>
                    <div class="toggle-desc">Muestra el campo de propina en el modal de pago</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="propina_habilitada" id="propina_habilitada"
                           {{ $settings['propina_habilitada'] === '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-row" id="row-propina-porcentaje" style="{{ $settings['propina_habilitada'] !== '1' ? 'opacity:0.4;pointer-events:none;' : '' }}">
                <div style="flex:1;">
                    <div class="toggle-label"><i class="bi bi-percent"></i> Porcentaje sugerido</div>
                    <div class="toggle-desc">Se pre-rellena automáticamente al abrir el modal (0 = sin sugerencia)</div>
                </div>
                <div style="width:100px; margin-left:1rem;">
                    <input type="number" name="propina_porcentaje" class="form-control-custom text-center"
                           value="{{ old('propina_porcentaje', $settings['propina_porcentaje']) }}"
                           min="0" max="100" step="1" placeholder="0">
                </div>
                <span class="ms-2 text-muted">%</span>
            </div>
        </div>
    </div>

    {{-- ── Opciones del Menú ────────────────────────────────────── --}}
    <div class="card-custom mb-4 fade-in">
        <div class="card-header-custom">
            <h2><i class="bi bi-toggles"></i> Opciones del Menú de Navegación</h2>
        </div>
        <div class="card-body-custom">
            <div class="toggle-row">
                <div>
                    <div class="toggle-label"><i class="bi bi-egg-fried"></i> Cocina</div>
                    <div class="toggle-desc">Muestra el enlace "Cocina" en el menú para todos los roles</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="menu_cocina_visible"
                           {{ $settings['menu_cocina_visible'] === '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-row">
                <div>
                    <div class="toggle-label"><i class="bi bi-clipboard-check"></i> Mis Pedidos</div>
                    <div class="toggle-desc">Muestra el enlace "Mis Pedidos" en el menú para meseros y admin</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="menu_mis_pedidos_visible"
                           {{ $settings['menu_mis_pedidos_visible'] === '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-success-custom">
            <i class="bi bi-check-lg"></i> Guardar Configuración
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.getElementById('propina_habilitada').addEventListener('change', function () {
    var row = document.getElementById('row-propina-porcentaje');
    row.style.opacity = this.checked ? '1' : '0.4';
    row.style.pointerEvents = this.checked ? '' : 'none';
});
document.getElementById('logoInput').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) {
        var preview = document.getElementById('logoPreview');
        var placeholder = document.getElementById('logoPlaceholder');
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
