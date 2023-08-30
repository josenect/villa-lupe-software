<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Producto;
use App\Models\ElementTable;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function show($id)
    {
        $mesa = Table::findOrFail($id);
        $productos = Producto::all()->where('status',1);

        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('table_id', $id)
        ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            // ... (tu código actual)
        
            // Calcula el subtotal para cada producto
            $subtotalProducto = ($producto->price - $producto->dicount) * $producto->amount;
            $subtotal += $subtotalProducto;
        
            // Calcula el descuento total
            $descuentoTotalProducto = $producto->dicount * $producto->amount;
            $descuentoTotal += $descuentoTotalProducto;
        }
        
        // Calcula el gran total
        $total = $subtotal - $descuentoTotal;
        
        
        return view('mesa', compact('mesa', 'productos','productosTable', 'subtotal','descuentoTotal', 'total'));
    }



    // Resto del código del controlador...
}