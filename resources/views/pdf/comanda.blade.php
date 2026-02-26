<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            width: 58mm;
            padding: 3mm;
            margin: 0;
        }

        .header-title { font-size: 16px; font-weight: bold; text-align: center; letter-spacing: 1px; }
        .header-sub   { font-size: 12px; text-align: center; }
        .header-hora  { font-size: 10px; text-align: center; color: #555; }

        .sep-doble  { border-top: 2px solid #000; margin: 3px 0; }
        .sep-simple { border-top: 1px dashed #000; margin: 3px 0; }

        /* Cabecera de sección de estado */
        .estado-header {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1px 3px;
            margin: 4px 0 2px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .estado-pendiente  { background:#000; color:#fff; }
        .estado-en-cocina  { background:#555; color:#fff; }
        .estado-listo      { background:#000; color:#fff; border-left: 3px solid #fff; }
        .estado-entregado  { background:#fff; color:#000; border: 1px solid #000; }

        /* Ítem */
        .item {
            display: flex;
            align-items: baseline;
            gap: 3px;
            margin-bottom: 1px;
        }
        .item-qty {
            font-size: 13px;
            font-weight: bold;
            min-width: 6mm;
            text-align: right;
            flex-shrink: 0;
        }
        .item-nombre {
            font-size: 13px;
            font-weight: bold;
            word-break: break-word;
            overflow-wrap: break-word;
            flex: 1;
        }
        .item-obs {
            font-size: 10px;
            padding-left: 9mm;
            font-style: italic;
            color: #444;
            margin-bottom: 1px;
        }

        .pie { font-size: 10px; text-align: center; margin-top: 2px; }

        @media print {
            @page { size: 58mm auto; margin: 0; }
            body  { width: 58mm; margin: 0; padding: 3mm; }
        }
    </style>
</head>
<body>
@php
    // Agrupar por estado → luego por producto sumando cantidades
    $orden  = ['pendiente' => 0, 'en_cocina' => 1, 'listo' => 2, 'entregado' => 3];
    $labels = [
        'pendiente' => 'Pendientes',
        'en_cocina' => 'En Cocina',
        'listo'     => 'Listos',
        'entregado' => 'Entregados',
    ];
    $clases = [
        'pendiente' => 'estado-pendiente',
        'en_cocina' => 'estado-en-cocina',
        'listo'     => 'estado-listo',
        'entregado' => 'estado-entregado',
    ];

    $secciones = [];
    foreach ($productosTable as $item) {
        $est = $item->estado;
        $pid = $item->producto_id;
        if (!isset($secciones[$est][$pid])) {
            $secciones[$est][$pid] = [
                'nombre'        => $item->producto->name,
                'cantidad'      => 0,
                'observaciones' => [],
            ];
        }
        $secciones[$est][$pid]['cantidad'] += $item->amount;
        if ($item->observacion) {
            $secciones[$est][$pid]['observaciones'][] = $item->observacion;
        }
    }

    // Ordenar secciones: pendiente primero
    uksort($secciones, fn($a, $b) => ($orden[$a] ?? 99) <=> ($orden[$b] ?? 99));

    $totalPlatos = $productosTable->sum('amount');
@endphp

<div class="header-title">COMANDA</div>
<div class="header-sub">{{ $mesa->name }}</div>
<div class="header-hora">{{ date('d/m/Y  H:i') }}</div>

<div class="sep-doble"></div>

@if($secciones)
    @foreach ($secciones as $estado => $productos)
        <div class="estado-header {{ $clases[$estado] ?? '' }}">
            {{ $labels[$estado] ?? strtoupper($estado) }}
        </div>

        @foreach ($productos as $plato)
            <div class="item">
                <div class="item-qty">{{ $plato['cantidad'] }}</div>
                <div class="item-nombre">{{ $plato['nombre'] }}</div>
            </div>
            @foreach ($plato['observaciones'] as $obs)
                <div class="item-obs">↳ {{ $obs }}</div>
            @endforeach
        @endforeach

        <div class="sep-simple"></div>
    @endforeach

    <div class="pie">{{ $totalPlatos }} platos en total</div>
@else
    <p style="text-align:center; font-size:12px; margin:6px 0;">Sin platos registrados</p>
@endif

<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
