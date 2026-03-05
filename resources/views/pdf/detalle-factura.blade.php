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
        
        .anulada-marca {
            color: red;
            font-weight: bold;
            text-align: center;
            border: 1px solid red;
            padding: 2px;
            margin: 2px 0;
            font-size: 12px;
        }

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
    @if($factura->estado !== 'activa')
        <div class="anulada-marca">
            *** FACTURA {{ strtoupper($factura->estado) }} ***
        </div>
    @endif
    
    @php
        $restNombre    = \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe');
        $restPropiedad = \App\Models\Setting::get('restaurante_propiedad', '');
        $restDireccion = \App\Models\Setting::get('restaurante_direccion', '');
        $restLogo      = \App\Models\Setting::get('restaurante_logo', '');
    @endphp
    <h1>{{ strtoupper($restNombre) }}</h1>
    @if($restPropiedad)<p>{{ $restPropiedad }}</p>@endif
    @if($restDireccion)<p>{{ $restDireccion }}</p>@endif
    <p>Fecha: {{ $factura->created_at }}</p>
    <h2>{{ $mesa->name }}</h2>
    @php $domInfo = $mesa->is_domicilio ? $mesa->domicilios()->latest()->first() : null; @endphp
    @if($domInfo)
    <p style="font-size:11px; font-weight:bold;">DOMICILIO</p>
    <p style="font-size:10px;">{{ $domInfo->cliente_nombre }}</p>
    <p style="font-size:10px;">Tel: {{ $domInfo->cliente_telefono }}</p>
    <p style="font-size:10px;">Dir: {{ $domInfo->cliente_direccion }}</p>
    @endif
    <h2>Factura: {{ $factura->numero_factura }}</h2>
    
    @if($factura->estado !== 'activa')
        <p style="color: red; font-size: 11px;">
            Anulada: {{ $factura->fecha_anulacion }}<br>
            {{ $factura->motivo_anulacion }}
        </p>
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
            @foreach ($productosFactura as $producto)
                <tr>
                    <td class="num" style="text-align:left;">{{ $producto->amount }}</td>
                    <td class="col-product" style="text-align:left;">{{ $producto->producto->name }}</td>
                    <td class="num" style="text-align:right;">{{ number_format(($producto->price - $producto->discount), 0, ',', '.') }}</td>
                    <td class="num" style="text-align:right;">{{ number_format(($producto->price - $producto->discount) * $producto->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:center;">--------------------------------</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Sub Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Propina V-5%:</strong></td>
                <td style="text-align:right;">{{ number_format($factura->valor_propina , 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total + $factura->valor_propina, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:center;">--------------------------------</td>
            </tr>
            @if($factura->medio_pago === 'Mixto')
                <tr>
                    <td colspan="2" style="text-align:left;"><strong>Efectivo:</strong></td>
                    <td colspan="2" style="text-align:right;">{{ number_format($factura->valor_efectivo, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:left;"><strong>Transferencia:</strong></td>
                    <td colspan="2" style="text-align:right;">{{ number_format($factura->valor_transferencia, 0, ',', '.') }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" style="text-align:center;"><strong>Pago: {{ $factura->medio_pago }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($factura->estado !== 'activa')
        <div class="anulada-marca">
            *** NO VÁLIDA ***
        </div>
    @endif
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
