<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Table; 
use App\Models\Producto; 
use App\Models\ElementTable;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    //


    public function generarPdf($mesa_id)
    {
        $mesa = Table::findOrFail($mesa_id);
        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('table_id', $mesa_id)
        ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            // ... (tu código actual)
        
            // Calcula el subtotal para cada producto
            $subtotalProducto = ($producto->price ) * $producto->amount;
            $subtotal += $subtotalProducto;
        
            // Calcula el descuento total
            $descuentoTotalProducto = $producto->dicount * $producto->amount;
            $descuentoTotal += $descuentoTotalProducto;
        }
        
        // Calcula el gran total
        $total = $subtotal - $descuentoTotal;
        

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true); // Habilitar el parser HTML5


        $dompdf = new Dompdf($options);
        $html = view('pdf.detalle-mesa', compact('mesa', 'productosTable', 'subtotal','descuentoTotal', 'total'))->render();
        $dompdf->loadHtml($html);
      // $dompdf->setPaper('custom', 'portrait');
       $dompdf->setPaper('A8', 'portrait'); // Tamaño de papel similar a 58 mm de ancho



        $dompdf->render();
        $dompdf->stream('detalle_mesa.pdf', ['Attachment' => false]);
    }


    public function visualPdf($mesa_id)
    {
        date_default_timezone_set('America/Bogota'); 
        $mesa = Table::findOrFail($mesa_id);
        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('table_id', $mesa_id)
        ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            // ... (tu código actual)
        
            // Calcula el subtotal para cada producto
            $subtotalProducto = $producto->price  * $producto->amount;
            $subtotal += $subtotalProducto;
        
            // Calcula el descuento total
            $descuentoTotalProducto = $producto->dicount * $producto->amount;
            $descuentoTotal += $descuentoTotalProducto;
        }
        
        // Calcula el gran total
        $total = $subtotal - $descuentoTotal;
        

        return view('pdf.detalle-mesa', compact('mesa', 'productosTable', 'subtotal','descuentoTotal', 'total'))->render();

    }
}
