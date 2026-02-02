<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Table; 
use App\Models\Producto; 
use App\Models\ElementTable;
use App\Models\LogImpresion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function generarPdf($mesa_id)
    {
        $mesa = Table::findOrFail($mesa_id);
        $productosTable = ElementTable::with('producto')
            ->where('status', 1)
            ->where('table_id', $mesa_id)
            ->where('estado', '!=', ElementTable::ESTADO_CANCELADO)
            ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            $subtotalProducto = ($producto->price) * $producto->amount;
            $subtotal += $subtotalProducto;
            $descuentoTotalProducto = $producto->dicount * $producto->amount;
            $descuentoTotal += $descuentoTotalProducto;
        }
        
        $total = $subtotal - $descuentoTotal;

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pdf.detalle-mesa', compact('mesa', 'productosTable', 'subtotal','descuentoTotal', 'total'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A8', 'portrait');
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
            ->where('estado', '!=', ElementTable::ESTADO_CANCELADO)
            ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            if ($producto->estado !== ElementTable::ESTADO_CANCELACION_SOLICITADA) {
                $subtotalProducto = $producto->price * $producto->amount;
                $subtotal += $subtotalProducto;
                $descuentoTotalProducto = $producto->dicount * $producto->amount;
                $descuentoTotal += $descuentoTotalProducto;
            }
        }
        
        $total = $subtotal - $descuentoTotal;

        // Registrar log de impresión
        if (Auth::check()) {
            LogImpresion::create([
                'user_id' => Auth::id(),
                'table_id' => $mesa_id,
                'tipo' => 'preliminar'
            ]);
        }

        return view('pdf.detalle-mesa', compact('mesa', 'productosTable', 'subtotal','descuentoTotal', 'total'))->render();
    }
}
