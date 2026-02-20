<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte — {{ $desde === $hasta ? $desde : $desde . ' al ' . $hasta }}</title>
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
    @php
        $restNombre    = \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe');
        $restPropiedad = \App\Models\Setting::get('restaurante_propiedad', '');
        $restDireccion = \App\Models\Setting::get('restaurante_direccion', '');
    @endphp
    <h1>{{ strtoupper($restNombre) }}</h1>
    @if($restPropiedad)<p>{{ $restPropiedad }}</p>@endif
    @if($restDireccion)<p>{{ $restDireccion }}</p>@endif
    <p>--------------------------------</p>
    <h2>REPORTE DEL DÍA</h2>
    <p>{{ $desde === $hasta ? $desde : $desde . ' al ' . $hasta }}</p>
    <p>--------------------------------</p>

    @if($tipo === 'facturas')
        <p><strong>** FACTURAS **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Fact.</th>
                    <th style="text-align:left;">Mesa</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($facturas as $factura)
                    @if($factura->estado === 'activa')
                    <tr>
                        <td style="text-align:left;">{{ $factura->numero_factura }}</td>
                        <td style="text-align:left;">{{ $factura->mesa->name ?? 'N/A' }}</td>
                        <td style="text-align:right;">{{ number_format($factura->valor_pagado, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <p>--------------------------------</p>
        <table>
            <tr>
                <td style="text-align:left;"><strong>Total Facturas:</strong></td>
                <td style="text-align:right;"><strong>{{ $facturas->where('estado', 'activa')->count() }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>Efectivo:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($totalEfectivo, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>Transferencia:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($totalTransferencia, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>Propinas:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($propinaTotal, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL VENTAS:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($facturasTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif

    @if($tipo === 'productos')
        <p><strong>** PRODUCTOS **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ud</th>
                    <th style="text-align:left;">Producto</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalleElementos as $producto)
                <tr>
                    <td style="text-align:left;">{{ $producto->cantidad }}</td>
                    <td style="text-align:left;">{{ $producto->name }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->precio - $producto->descuento) * $producto->cantidad, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>--------------------------------</p>
        <table>
            <tr>
                <td style="text-align:left;"><strong>Total Uds:</strong></td>
                <td style="text-align:right;"><strong>{{ $totalProductos }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($totalPrecio, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif

    {{-- Reporte por categoría específica --}}
    @foreach($categorias as $cat)
    @if($tipo === 'cat-'.$cat->slug)
        <p><strong>** {{ strtoupper($cat->nombre) }} **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ud</th>
                    <th style="text-align:left;">Producto</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categoriaData[$cat->slug]['productos'] as $producto)
                <tr>
                    <td style="text-align:left;">{{ $producto->cantidad }}</td>
                    <td style="text-align:left;">{{ $producto->name }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->precio - $producto->descuento) * $producto->cantidad, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>--------------------------------</p>
        <table>
            <tr>
                <td style="text-align:left;"><strong>Total Uds:</strong></td>
                <td style="text-align:right;"><strong>{{ $categoriaData[$cat->slug]['totalProductos'] }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($categoriaData[$cat->slug]['totalPrecio'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif
    @endforeach

    {{-- Reporte toda la cocina --}}
    @if($tipo === 'cocina')
        <p><strong>** TODA LA COCINA **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ud</th>
                    <th style="text-align:left;">Producto</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cocinaTodo['productos'] as $producto)
                <tr>
                    <td style="text-align:left;">{{ $producto->cantidad }}</td>
                    <td style="text-align:left;">{{ $producto->name }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->precio - $producto->descuento) * $producto->cantidad, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>--------------------------------</p>
        <table>
            <tr>
                <td style="text-align:left;"><strong>Total Uds:</strong></td>
                <td style="text-align:right;"><strong>{{ $cocinaTodo['totalProductos'] }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($cocinaTodo['totalPrecio'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif

    <p>--------------------------------</p>
    <p>Impreso: {{ date('d/m/Y H:i') }}</p>
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
