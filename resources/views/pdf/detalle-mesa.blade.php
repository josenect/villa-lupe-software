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
            @foreach ($productosTable as $producto)
                @if($producto->estado !== 'cancelacion_solicitada' && $producto->estado !== 'cancelado')
                <tr>
                    <td style="text-align:left;">{{ $producto->amount }}</td>
                    <td style="text-align:left;">{{ $producto->producto->name }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->price - $producto->dicount), 0, ',', '.') }}</td>
                    <td style="text-align:right;">{{ number_format(($producto->price - $producto->dicount) * $producto->amount, 0, ',', '.') }}</td>
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
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Sub Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Propina V-5%:</strong></td>
                <td style="text-align:right;">{{ number_format(($total * env('PROPINA') )/100, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                <td style="text-align:right;">{{ number_format($total + (($total * env('PROPINA'))/100), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
