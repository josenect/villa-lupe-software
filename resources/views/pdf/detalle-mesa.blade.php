<!DOCTYPE html>
<html>
<head>
    <style>
        /* Definir estilos CSS para el PDF aquí */
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            font-size: 14px;

            
        }


        @media print {
        thead {
            display: table-header-group;
        }
    }
        /* ... Otros estilos ... */
    </style>
</head>
<body>
    <div>
        
        <h1 style="text-align: center; margin-right:0;margin-bottom: 0px;"> VILLA LUPE</h1>
        <p style="text-align: center;margin-top: 0px;margin-bottom: 0px;"> Casa de Campo</p>
        <p style="text-align: center;margin-top: 0px">Fecha : {{ date('d/m/Y H:i') }}</p>
        <h2  style="text-align: center;"> {{ $mesa->name }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Uds</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <!-- ... Otros encabezados ... -->
                </tr>
            </thead>
            <tbody>
                @foreach ($productosTable as $producto)
                    <tr>
                        <td style="text-align: start;">{{ $producto->amount }}</td>
                        <td  style="text-align: start;">{{ $producto->producto->name }}</td>
                        <td style="text-align: right;">{{ number_format(($producto->price - $producto->dicount), 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format(($producto->price - $producto->dicount) * $producto->amount, 0, ',', '.') }}</td>
                        <!-- ... Otros datos ... -->
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right;"></td>
                </tr>
                <tr>
                   
                    <td colspan="3" style="text-align: right;"><strong>Sub Total:</strong></td>
                    <td style="text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
                    <!-- Otras celdas en esta fila -->
                </tr>
                <!-- Fila para Descuento Total -->
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Propina V-5%:</strong></td>
                    <td style="text-align: right;">{{ number_format(($total * env('PROPINA') )/100, 0, ',', '.') }}</td>
                    <!-- Otras celdas en esta fila -->
                </tr>
                <!-- Fila para Total -->
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td style="text-align: right;">{{ number_format($total + (($total * env('PROPINA'))/100), 0, ',', '.') }}</td>
                    <!-- Otras celdas en esta fila -->
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <!-- ... Otros totales ... -->
                </tr>
            </tfoot>
        </table>

    </div>
</body>
</html>
