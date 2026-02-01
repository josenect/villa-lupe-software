<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte del Día - {{ $date }}</title>
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
    <h1>VILLA LUPE</h1>
    <p>Casa de Campo</p>
    <p>--------------------------------</p>
    <h2>REPORTE DEL DÍA</h2>
    <p>{{ $date }}</p>
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

    @if($tipo === 'cocina')
        <p><strong>** COCINA ALMUERZOS **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ud</th>
                    <th style="text-align:left;">Producto</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalleCocinaAlmu as $producto)
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
                <td style="text-align:right;"><strong>{{ $cocinaTotalProductosAlmu }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($cocinaTotalPrecioAlmu, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif

    @if($tipo === 'cocina-productos')
        <p><strong>** COCINA PRODUCTOS **</strong></p>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ud</th>
                    <th style="text-align:left;">Producto</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalleCocina as $producto)
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
                <td style="text-align:right;"><strong>{{ $cocinaTotalProductos }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left;"><strong>TOTAL:</strong></td>
                <td style="text-align:right;"><strong>$ {{ number_format($cocinaTotalPrecio, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    @endif

    <p>--------------------------------</p>
    <p>Impreso: {{ date('d/m/Y H:i') }}</p>
</body>
</html>
