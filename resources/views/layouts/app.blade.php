<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Villa Lupe')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        /* Navbar Styles */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--card-shadow);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .navbar-brand i {
            color: var(--accent-color);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--primary-color) !important;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            margin: 0 0.2rem;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: var(--secondary-color);
            color: white !important;
        }
        
        .nav-link i {
            margin-right: 0.5rem;
        }
        
        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 12px;
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        /* Main Container */
        .main-container {
            padding: 2rem 0;
        }
        
        /* Card Styles */
        .card-custom {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: none;
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border: none;
        }
        
        .card-header-custom h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body-custom {
            padding: 1.5rem;
        }
        
        /* Page Title */
        .page-title {
            color: white;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.5rem;
        }
        
        /* Button Styles */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-danger-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
            color: white;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success-color), #1e8449);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-warning-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
            color: white;
        }
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.4);
            color: white;
        }
        
        /* Table Styles */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
        }
        
        .table-custom thead th:first-child {
            border-radius: 10px 0 0 0;
        }
        
        .table-custom thead th:last-child {
            border-radius: 0 10px 0 0;
        }
        
        .table-custom tbody tr {
            transition: var(--transition);
        }
        
        .table-custom tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .table-custom tbody td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label-custom {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            width: 100%;
        }
        
        .form-control-custom:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .form-select-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            width: 100%;
            background-color: white;
        }
        
        .form-select-custom:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        /* Alert Styles */
        .alert-custom {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success-custom {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--success-color);
        }
        
        .alert-error-custom {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--accent-color);
        }
        
        /* Mesa Card (for grid view) */
        .mesa-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
        }
        
        .mesa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .mesa-card.ocupada {
            border-left: 5px solid var(--accent-color);
            background: linear-gradient(135deg, #fff5f5, white);
        }
        
        .mesa-card.disponible {
            border-left: 5px solid var(--success-color);
        }
        
        .mesa-card h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .mesa-info {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .mesa-info i {
            width: 20px;
            color: var(--secondary-color);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge.ocupada {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--accent-color);
        }
        
        .status-badge.disponible {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--success-color);
        }
        
        .status-badge.activo {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--success-color);
        }
        
        .status-badge.inactivo {
            background-color: rgba(149, 165, 166, 0.15);
            color: #7f8c8d;
        }
        
        /* Search Box */
        .search-box {
            position: relative;
            display: inline-block;
        }
        
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .search-box input {
            padding-left: 35px !important;
        }
        
        /* Action Buttons Container */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-sm-custom {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        
        /* Footer */
        .footer-custom {
            background: rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
        
        /* ===================== RESPONSIVE STYLES ===================== */
        
        /* Tablets y móviles grandes */
        @media (max-width: 991px) {
            .main-container {
                padding: 1rem 0;
            }
            
            .card-header-custom {
                padding: 1rem;
            }
            
            .card-body-custom {
                padding: 1rem;
            }
        }
        
        /* Móviles */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.3rem;
            }
            
            /* Header responsive */
            .d-flex.justify-content-between.align-items-center.mb-4 {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch !important;
            }
            
            .d-flex.justify-content-between.align-items-center.mb-4 > * {
                text-align: center;
            }
            
            /* Navbar móvil */
            .navbar-custom {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-nav {
                padding: 1rem 0;
            }
            
            .nav-link {
                padding: 0.75rem 1rem !important;
                margin: 0.2rem 0;
                text-align: center;
            }
            
            /* Cards responsive */
            .card-header-custom h2 {
                font-size: 1.1rem;
            }
            
            /* Tablas responsive - Convertir a tarjetas en móvil */
            .table-responsive {
                border-radius: 12px;
                overflow: visible;
            }
            
            .table-custom {
                display: block;
            }
            
            .table-custom thead {
                display: none;
            }
            
            .table-custom tbody {
                display: block;
            }
            
            .table-custom tbody tr {
                display: block;
                background: white;
                margin-bottom: 1rem;
                padding: 1rem;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                border-left: 4px solid var(--secondary-color);
            }
            
            .table-custom tbody tr:hover {
                transform: none;
            }
            
            .table-custom tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
                border-bottom: 1px solid #eee;
                text-align: right;
            }
            
            .table-custom tbody td:last-child {
                border-bottom: none;
                padding-top: 1rem;
            }
            
            .table-custom tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--primary-color);
                text-align: left;
                flex: 1;
                font-size: 0.85rem;
            }
            
            .table-custom tfoot {
                display: block;
            }
            
            .table-custom tfoot tr {
                display: block;
                padding: 1rem;
                border-radius: 12px;
            }
            
            .table-custom tfoot td {
                display: block;
                padding: 0.5rem 0;
                text-align: center !important;
                border: none;
            }
            
            .table-custom tfoot td[colspan] {
                display: block;
            }
            
            /* Botones de acción en móvil */
            .action-buttons {
                flex-direction: row;
                justify-content: center;
                gap: 0.5rem;
                width: 100%;
                flex-wrap: wrap;
            }
            
            .btn-sm-custom {
                flex: 1;
                min-width: 45px;
                max-width: 60px;
                justify-content: center;
                padding: 0.6rem;
            }
            
            /* Botones generales más grandes en móvil */
            .btn-primary-custom,
            .btn-danger-custom,
            .btn-success-custom,
            .btn-warning-custom,
            .btn-secondary-custom {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                justify-content: center;
            }
            
            /* Formularios responsive */
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-control-custom,
            .form-select-custom {
                padding: 0.9rem 1rem;
                font-size: 16px; /* Evita zoom en iOS */
            }
            
            /* Modales responsive */
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-body {
                padding: 1rem !important;
            }
            
            /* Stats cards */
            .contador-pedidos {
                font-size: 2rem;
            }
            
            /* Mesa card responsive */
            .mesa-card {
                padding: 1rem;
            }
            
            .mesa-card h4 {
                font-size: 1rem;
            }
            
            /* Search box */
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            /* Footer */
            .footer-custom {
                font-size: 0.8rem;
                padding: 0.75rem;
            }
        }
        
        /* Móviles pequeños */
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.1rem;
            }
            
            .card-header-custom h2 {
                font-size: 1rem;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            /* Hacer botones de acción más táctiles */
            .btn-sm-custom {
                padding: 0.7rem;
                min-width: 50px;
            }
            
            .action-buttons {
                gap: 0.4rem;
            }
            
            /* Badges más legibles */
            .badge {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }
        }
        
        /* Mejoras táctiles generales */
        @media (hover: none) and (pointer: coarse) {
            /* Dispositivos táctiles */
            .btn-primary-custom,
            .btn-danger-custom,
            .btn-success-custom,
            .btn-warning-custom,
            .btn-secondary-custom,
            .btn-sm-custom {
                min-height: 44px; /* Mínimo recomendado por Apple/Google */
            }
            
            .nav-link {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            /* Quitar efectos hover que no aplican en táctiles */
            .card-custom:hover,
            .mesa-card:hover,
            .pedido-card:hover {
                transform: none;
            }
        }
        
        /* Print styles */
        @media print {
            .navbar-custom,
            .footer-custom,
            .btn-primary-custom,
            .btn-danger-custom,
            .btn-success-custom,
            .btn-warning-custom,
            .btn-secondary-custom,
            .action-buttons {
                display: none !important;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Section Title */
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--secondary-color);
            display: inline-block;
        }
        
        /* Total Row */
        .total-row {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .total-row td {
            border-bottom: none !important;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="@auth @if(auth()->user()->esCocina()) {{ route('cocina.index') }} @else / @endif @else / @endauth">
                <i class="bi bi-shop"></i> Villa Lupe
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->esAdmin() || auth()->user()->esMesero())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                                    <i class="bi bi-house-door"></i> Inicio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('mesero/pedidos*') ? 'active' : '' }}" href="{{ route('mesero.pedidos') }}">
                                    <i class="bi bi-clipboard-check"></i> Mis Pedidos
                                    @php
                                        $pedidosListosMesero = \App\Models\ElementTable::where('status', 1)
                                            ->where('estado', 'listo')
                                            ->where('user_id', auth()->id())
                                            ->count();
                                    @endphp
                                    @if($pedidosListosMesero > 0)
                                        <span class="badge bg-success">{{ $pedidosListosMesero }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        
                        @if(auth()->user()->esAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/mesas*') ? 'active' : '' }}" href="/admin/mesas">
                                    <i class="bi bi-grid-3x3"></i> Mesas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/productos*') ? 'active' : '' }}" href="/admin/productos">
                                    <i class="bi bi-box-seam"></i> Productos
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/facturas*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-receipt"></i> Reportes
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="/admin/facturas/{{ date('Y-m-d') }}">
                                            <i class="bi bi-calendar-day"></i> Facturas del Día
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/admin/facturas/{{ date('Y-m-d') }}?data=productos">
                                            <i class="bi bi-bar-chart"></i> Reporte Productos
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/admin/facturas/{{ date('Y-m-d') }}?data=cocina">
                                            <i class="bi bi-egg-fried"></i> Reporte Cocina
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/usuarios*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            @php
                                $cancelacionesPendientes = \App\Models\ElementTable::where('estado', 'cancelacion_solicitada')->count();
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/cancelaciones*') ? 'active' : '' }}" href="{{ route('admin.cancelaciones.pendientes') }}" style="{{ $cancelacionesPendientes > 0 ? 'color: #e74c3c !important;' : '' }}">
                                    <i class="bi bi-x-circle"></i>
                                    @if($cancelacionesPendientes > 0)
                                        <span class="badge bg-danger">{{ $cancelacionesPendientes }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/pedidos-meseros*') ? 'active' : '' }}" href="{{ route('admin.pedidos.meseros') }}" title="Pedidos por Mesero">
                                    <i class="bi bi-people"></i>
                                </a>
                            </li>
                        @endif
                        
                        @if(auth()->user()->esCocina() || auth()->user()->esAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('cocina*') ? 'active' : '' }}" href="{{ route('cocina.index') }}">
                                    <i class="bi bi-egg-fried"></i> Cocina
                                </a>
                            </li>
                        @endif
                        
                        <!-- Usuario y Logout -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small class="text-muted">
                                            @if(auth()->user()->esAdmin())
                                                <i class="bi bi-shield-check"></i> Administrador
                                            @elseif(auth()->user()->esMesero())
                                                <i class="bi bi-person-badge"></i> Mesero
                                            @else
                                                <i class="bi bi-egg-fried"></i> Cocina
                                            @endif
                                        </small>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-container">
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer-custom">
        <p class="mb-0">&copy; {{ date('Y') }} Villa Lupe - Sistema de Gestión de Restaurante</p>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
