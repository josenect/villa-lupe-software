<!DOCTYPE html>
<html>
<head>
    <style>
        /* Definir estilos CSS para el PDF aquí */
        body {
            font-family: "Courier New", monospace;
            font-size: 13px;

            
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
        <p style="text-align: center;margin-top: 0px">Total Porductos</p>

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
                @foreach ($detalleElementos as $producto)
                    <tr>
                        <td style="text-align: start;">{{ $producto->cantidad }}</td>
                        <td  style="text-align: start;">{{ $producto->name }}</td>
                        <td style="text-align: right;">{{ number_format($producto->precio, 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format($producto->precio * $producto->cantidad, 0, ',', '.') }}</td>
                        <!-- ... Otros datos ... -->
                    </tr>
                @endforeach
                <tr>
                    
                   
                        <td colspan="3" style="text-align: right;"><strong>Total Produtos:</strong></td>
                        <td style="text-align: right;">{{ $totalProductos}}</td>
                        <!-- Otras celdas en esta fila -->
                <tr>
                    <tr>
                   
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td style="text-align: right;">{{ number_format($totalPrecio, 0, ',', '.') }}</td>
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
