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
        }
        
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
    
    <h1>VILLA LUPE</h1>
    <p>Casa de Campo</p>
    <p>Fecha: {{ $factura->created_at }}</p>
    <h2>{{ $mesa->name }}</h2>
    <h2>Factura: {{ $factura->numero_factura }}</h2>
    
    @if($factura->estado !== 'activa')
        <p style="color: red; font-size: 11px;">
            Anulada: {{ $factura->fecha_anulacion }}<br>
            {{ $factura->motivo_anulacion }}
        </p>
    @endif
    
    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Ud</th>
                <th style="text-align:left;">Producto</th>
                <th style="text-align:right;">Precio</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productosFactura as $producto)
                <tr>
                    <td style="text-align:left;">{{ $producto->amount }}</td>
                    <td style="text-align:left;">{{ $producto->producto->name }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->price - $producto->discount), 0, ',', '.') }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->price - $producto->discount) * $producto->amount, 0, ',', '.') }}</td>
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
        </tbody>
    </table>

    @if($factura->estado !== 'activa')
        <div class="anulada-marca">
            *** NO VÁLIDA ***
        </div>
    @endif
</body>
</html>
