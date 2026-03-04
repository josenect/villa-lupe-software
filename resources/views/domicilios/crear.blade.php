@extends('layouts.app')

@section('title', 'Nuevo Domicilio')

@section('content')

<div class="text-center mb-4 fade-in">
    <h1 class="page-title"><i class="bi bi-truck"></i> Nuevo Domicilio</h1>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card-custom fade-in">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                <h2><i class="bi bi-person-plus"></i> Datos del Cliente</h2>
            </div>
            <div class="card-body-custom">
                @if($errors->any())
                    <div class="alert-custom alert-error-custom mb-3">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('domicilios.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label-custom"><i class="bi bi-person"></i> Nombre del Cliente</label>
                        <input type="text" name="cliente_nombre" class="form-control-custom" value="{{ old('cliente_nombre') }}" placeholder="Nombre completo" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom"><i class="bi bi-telephone"></i> Telefono</label>
                        <input type="text" name="cliente_telefono" class="form-control-custom" value="{{ old('cliente_telefono') }}" placeholder="Numero de telefono" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom"><i class="bi bi-geo-alt"></i> Direccion</label>
                        <input type="text" name="cliente_direccion" class="form-control-custom" value="{{ old('cliente_direccion') }}" placeholder="Direccion de entrega" required>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-success-custom flex-fill justify-content-center">
                            <i class="bi bi-check-lg"></i> Crear Domicilio
                        </button>
                        <a href="{{ route('domicilios.index') }}" class="btn-secondary-custom justify-content-center">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
