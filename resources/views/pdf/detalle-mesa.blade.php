<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            width: 58mm;
            padding: 3mm;
            margin: 0;
        }
        
        h1 {
            font-size: 16px;
            text-align: center;
            margin: 0;
        }
        
        h2 {
            font-size: 14px;
            text-align: center;
            margin: 0;
        }
        
        p {
            margin: 0;
            padding: 0;
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            font-size: 13px;
            padding: 1px 0;
            vertical-align: top;
        }

        .num { width: 1%; white-space: nowrap; padding-left: 4px; }
        .col-product { word-break: break-word; overflow-wrap: break-word; padding-right: 2px; }

        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }

            body {
                width: 58mm;
                margin: 0;
                padding: 3mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $restNombre    = \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe');
        $restPropiedad = \App\Models\Setting::get('restaurante_propiedad', '');
        $restDireccion = \App\Models\Setting::get('restaurante_direccion', '');
    @endphp
    <h1>{{ strtoupper($restNombre) }}</h1>
    @if($restPropiedad)<p>{{ $restPropiedad }}</p>@endif
    @if($restDireccion)<p>{{ $restDireccion }}</p>@endif
    <p>Fecha: {{ date('d/m/Y H:i') }}</p>
    <h2>{{ $mesa->name }}</h2>
    @php $domInfo = $mesa->is_domicilio ? $mesa->domicilios()->latest()->first() : null; @endphp
    @if($domInfo)
    <p style="font-size:11px; font-weight:bold;">DOMICILIO</p>
    <p style="font-size:10px;">{{ $domInfo->cliente_nombre }}</p>
    <p style="font-size:10px;">Tel: {{ $domInfo->cliente_telefono }}</p>
    <p style="font-size:10px;">Dir: {{ $domInfo->cliente_direccion }}</p>
    @endif
    <table style="width: 100%;">
        <thead>
            <tr>
                <th class="num" style="text-align:left;">Ud</th>
                <th style="text-align:left;">Producto</th>
                <th class="num" style="text-align:right;">Precio</th>
                <th class="num" style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productosTable as $producto)
                @if($producto->estado !== 'cancelacion_solicitada' && $producto->estado !== 'cancelado')
                <tr>
                    <td class="num" style="text-align:left;">{{ $producto->amount }}</td>
                    <td class="col-product" style="text-align:left;">{{ $producto->producto->name }}</td>
                    <td class="num" style="text-align:right;">{{ number_format(($producto->price - $producto->dicount), 0, ',', '.') }}</td>
                    <td class="num" style="text-align:right;">{{ number_format(($producto->price - $producto->dicount) * $producto->amount, 0, ',', '.') }}</td>
                </tr>
                @if($producto->observacion)
                <tr>
                    <td colspan="4" style="text-align:left; font-size: 11px; padding-left: 10px;"><em>* {{ $producto->observacion }}</em></td>
                </tr>
                @endif
                @endif
            @endforeach
            <tr>
                <td colspan="4" style="text-align:center;">--------------------------------</td>
            </tr>
            @php
                $propinaHabilitada = \App\Models\Setting::get('propina_habilitada', '1') === '1';
                $propinaPct        = (int) \App\Models\Setting::get('propina_porcentaje', env('PROPINA', 10));
                $propinaValor      = $propinaHabilitada ? (int)(floor($total * $propinaPct / 100 / 1000) * 1000) : 0;
            @endphp
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Sub Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @if($propinaHabilitada)
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Propina {{ $propinaPct }}%:</strong></td>
                <td style="text-align:right;">{{ number_format($propinaValor, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total + $propinaValor, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
