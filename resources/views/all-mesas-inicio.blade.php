@extends('layouts.app')

@section('title', 'Villa Lupe - Inicio')

@section('content')
{{date_default_timezone_set('America/Bogota')}}

@php
    // Convertir a array si es necesario y contar
    $tablesArray = is_array($tables) ? $tables : $tables->toArray();
    $totalMesas = count($tablesArray);
    $mesasOcupadas = count(array_filter($tablesArray, function($t) {
        return (is_object($t) ? $t->status : $t['status']) == 'Ocupada';
    }));
    $mesasDisponibles = $totalMesas - $mesasOcupadas;
@endphp

<div class="text-center mb-4 fade-in">
    <h1 class="page-title">
        <i class="bi bi-shop"></i> Bienvenido a Villa Lupe
    </h1>
    <p class="text-white opacity-75">Sistema de Gestión de Mesas y Pedidos</p>
</div>

@if(session('success'))
    <div class="alert-custom alert-success-custom fade-in">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-custom fade-in">
    <div class="card-header-custom">
        <h2><i class="bi bi-grid-3x3-gap"></i> Estado de las Mesas</h2>
    </div>
    <div class="card-body-custom">
        <div class="row g-4">
            @foreach ($tables as $table)
                @php
                    $tableStatus = is_object($table) ? $table->status : $table['status'];
                    $tableName = is_object($table) ? $table->name : $table['name'];
                    $tableLocation = is_object($table) ? $table->location : $table['location'];
                    $tableId = is_object($table) ? $table->id : $table['id'];
                @endphp
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="mesa-card {{ $tableStatus == 'Ocupada' ? 'ocupada' : 'disponible' }}">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="mb-0">
                                <i class="bi bi-table"></i> {{ $tableName }}
                            </h4>
                            <span class="status-badge {{ $tableStatus == 'Ocupada' ? 'ocupada' : 'disponible' }}">
                                {{ $tableStatus }}
                            </span>
                        </div>
                        
                        <div class="mesa-info">
                            <i class="bi bi-geo-alt"></i> {{ $tableLocation }}
                        </div>
                        
                        <div class="mt-3">
                            <a href="mesa/{{ $tableId }}" class="btn-primary-custom w-100 justify-content-center">
                                <i class="bi bi-pencil-square"></i> Gestionar Mesa
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($totalMesas == 0)
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No hay mesas registradas</p>
                <a href="/admin/mesas" class="btn-primary-custom">
                    <i class="bi bi-plus-circle"></i> Agregar Mesa
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mt-4 fade-in">
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-success mb-1">{{ $mesasDisponibles }}</h3>
                <p class="text-muted mb-0">Mesas Disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-3">
                    <i class="bi bi-clock text-danger" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-danger mb-1">{{ $mesasOcupadas }}</h3>
                <p class="text-muted mb-0">Mesas Ocupadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="card-body-custom text-center">
                <div class="mb-3">
                    <i class="bi bi-grid-3x3 text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-primary mb-1">{{ $totalMesas }}</h3>
                <p class="text-muted mb-0">Total de Mesas</p>
            </div>
        </div>
    </div>
</div>
@endsection
