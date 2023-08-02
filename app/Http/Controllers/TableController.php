<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Producto;
use App\Models\ElementTable;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function show($id)
    {
        $mesa = Table::findOrFail($id);
        $productos = Producto::all()->where('status',1);
        $productosTable = ElementTable::all()->where('status',1)->where('table_id',$id);


dd($productosTable );

        return view('mesa', compact('mesa', 'productos'));
    }

    // Resto del código del controlador...
}