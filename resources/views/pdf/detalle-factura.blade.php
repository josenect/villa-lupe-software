<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            /* Definir estilos CSS para el PDF aquí */
           
            body {
                font-family: Arial, sans-serif; /* Fuente estándar compatible */
                font-size: 12px;
            }   
    
    
        
            @media print {
                    @page {
                        size: 44mm auto; /* Ancho fijo, altura flexible */
                        margin: 0; /* Elimina márgenes automáticos */
                    }
    
                    body {
                        width: 44mm;
                        min-height: 150mm; /* Mínimo de 150mm (ajusta según necesites) */
                        font-size: 12px;
                        margin: 0;
                        padding: 2mm;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between; /* Distribuye contenido */
                    }
                    .content {
            flex-grow: 1; /* Hace que el contenido crezca para llenar el espacio */
        }
    
                h1, h2, p {
                    text-align: center;
                    margin: 2px 0;
                }
    
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
    
                th, td {
                    font-size: 12px; /* Ajuste de tamaño de texto */
                    padding: 2px 4px;
                }
    
                th {
                    text-align: left;
                }
    
                td {
                    text-align: right;
                }
    
                /* Opcional: Ajustar la visibilidad de elementos no imprimibles */
                .no-print {
                    display: none;
                }
            }
    
        
            /* ... Otros estilos ... */
        </style>
    </head>
<body>
    <div>
        
        <h1 style="text-align: center; margin-right:0;margin-bottom: 0px;"> VILLA LUPE</h1>
        <p style="text-align: center;margin-top: 0px;margin-bottom: 0px;"> Casa de Campo</p>
        <p style="text-align: center;margin-top: 0px">Fecha : {{ $factura->created_at }}</p>
        <h2  style="text-align: center;"> {{ $mesa->name }}</h2>
        <h2  style="text-align: center;"> Factura : {{ $factura->numero_factura }}</h2>
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
                @foreach ($productosFactura as $producto)
                    <tr>
                        <td style="text-align: start;">{{ $producto->amount }}</td>
                        <td  style="text-align: start;">{{ $producto->producto->name }}</td>
                        <td style="text-align: right;">{{ number_format(($producto->price - $producto->discount), 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format(($producto->price - $producto->discount) * $producto->amount, 0, ',', '.') }}</td>
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
                    <td style="text-align: right;">{{ number_format($factura->valor_propina , 0, ',', '.') }}</td>
                    <!-- Otras celdas en esta fila -->
                </tr>
                <!-- Fila para Total -->
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td style="text-align: right;">{{ number_format($total + $factura->valor_propina, 0, ',', '.') }}</td>
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
