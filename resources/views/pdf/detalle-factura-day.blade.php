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
        @if(request()->has('data') && request()->get('data') === 'productos')
        <h1 style="text-align: center; margin-right:0;margin-bottom: 0px;"> VILLA LUPE</h1>
        <p style="text-align: center;margin-top: 0px;margin-bottom: 0px;"> Casa de Campo</p>
        <h2 style="text-align: center;margin-top: 0px">Total productos vendidos</h2>

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
                        <td style="text-align: right;">{{ number_format($producto->precio - $producto->descuento, 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format(($producto->precio - $producto->descuento ) * $producto->cantidad, 0, ',', '.') }}</td>
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
        @endif
        <br>
        <br>
        @if(request()->has('data') && request()->get('data') === 'facturas')
        <h2 style="text-align: center;margin-top: 0px">Facturas </h2>

        <table>
            <thead>
                <tr>
                    <th>Numero Factura</th>
                    <th>Valor</th>
                    <th>Propina</th>
                    <th>Total</th>
                    <!-- ... Otros encabezados ... -->
                </tr>
            </thead>
            <tbody>
                @foreach ($facturas as $factura)
                    <tr>
                        <td style="text-align: start;">{{ $factura->numero_factura }}</td>
                        <td style="text-align: right;">{{ number_format($factura->valor_total, 0, ',', '.') }}</td>

                        <td style="text-align: right;">{{ number_format($factura->valor_propina, 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format($factura->valor_pagado, 0, ',', '.') }}</td>
                        <!-- ... Otros datos ... -->
                    </tr>
                @endforeach

                    <tr>
                   
                        <td colspan="3" style="text-align: right;"><strong>Total facturas:</strong></td>
                        <td style="text-align: right;">{{ number_format($facturasTotal, 0, ',', '.') }}</td>
                        <!-- Otras celdas en esta fila -->
                    </tr>
                
            </tbody>
            <tfoot>
                <tr>
                    <!-- ... Otros totales ... -->
                </tr>
            </tfoot>
        </table>
        @endif
        <br>
        <br>
        @if(request()->has('data') && request()->get('data') === 'cocina-productos')
        <h2 style="text-align: center;margin-top: 0px">Productos restaurante </h2>

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
                @foreach ($detalleCocina as $productoCocina)
                    <tr>
                        <td style="text-align: start;">{{ $productoCocina->cantidad }}</td>
                        <td  style="text-align: start;">{{ $productoCocina->name }}</td>
                        <td style="text-align: right;">{{ number_format($productoCocina->precio - $productoCocina->descuento, 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format(($productoCocina->precio - $productoCocina->descuento ) * $productoCocina->cantidad, 0, ',', '.') }}</td>
                        <!-- ... Otros datos ... -->
                    </tr>
                @endforeach
                <tr>
                    
                   
                        <td colspan="3" style="text-align: right;"><strong>Total Produtos:</strong></td>
                        <td style="text-align: right;">{{ $cocinaTotalProductos}}</td>
                        <!-- Otras celdas en esta fila -->
                <tr>
                    <tr>
                   
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td style="text-align: right;">{{ number_format($cocinaTotalPrecio, 0, ',', '.') }}</td>
                        <!-- Otras celdas en esta fila -->
                    </tr>
                
            </tbody>
            <tfoot>
                <tr>
                    <!-- ... Otros totales ... -->
                </tr>
            </tfoot>
        </table>
        @endif
        <br>
        <br>
        @if(request()->has('data') && request()->get('data') === 'cocina')
        <h2  style="text-align: center;margin-top: 0px">Productos restaurante almuerzos </h2>

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
                @foreach ($detalleCocinaAlmu as $productoCocina)
                    <tr>
                        <td style="text-align: start;">{{ $productoCocina->cantidad }}</td>
                        <td  style="text-align: start;">{{ $productoCocina->name }}</td>
                        <td style="text-align: right;">{{ number_format($productoCocina->precio - $productoCocina->descuento, 0, ',', '.') }}</td>
                        <td  style="text-align: right;">{{ number_format(($productoCocina->precio - $productoCocina->descuento ) * $productoCocina->cantidad, 0, ',', '.') }}</td>
                        <!-- ... Otros datos ... -->
                    </tr>
                @endforeach
                <tr>
                    
                   
                        <td colspan="3" style="text-align: right;"><strong>Total Produtos:</strong></td>
                        <td style="text-align: right;">{{ $cocinaTotalProductosAlmu}}</td>
                        <!-- Otras celdas en esta fila -->
                <tr>
                    <tr>
                   
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td style="text-align: right;">{{ number_format($cocinaTotalPrecioAlmu, 0, ',', '.') }}</td>
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
    @endif
</body>
</html>
