<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElementTable;
use App\Models\Producto; 
use App\Models\Table; 

 // Asegúrate de importar el modelo Producto

class ProductoController extends Controller
{
    /**
     * Almacena un nuevo producto en la mesa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInTable(Request $request)
    {
        // Validar los datos recibidos del formulario (si es necesario).
        $request->validate([
            'mesa_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|integer',
        ]);


        $product = Producto::findOrFail($request->input('product_id'));

        if($request->input('dicount')>$product->price){
            return redirect()->route('mesa.store',$request->input('mesa_id'))->with('success', 'El Valor del Descuento es mayor al valor del Producto.');

        }
        date_default_timezone_set('America/Bogota');
        // Crear un nuevo registro en la tabla 'productos'.
        $productoTable = new ElementTable();
        $productoTable->price = $product->price;
        $productoTable->table_id = $request->input('mesa_id');
        $productoTable->producto_id = $request->input('product_id');
        $productoTable->amount = $request->input('amount');
        $productoTable->dicount = $request->input('dicount')?? 0;
        $productoTable->record = date('Y-m-d H:i:s');
        $productoTable->status = true;
        $productoTable->save();
        // Puedes agregar algún mensaje de éxito o redireccionar a otra página después de guardar el producto.
        return redirect()->route('mesa.store',$request->input('mesa_id'))->with('success', 'El producto ha sido Cargado exitosamente.');
    }


    public function edit($mesa_id, $id)
    {
        $mesa = Table::findOrFail($mesa_id);
        $producto = ElementTable::findOrFail($id);
        $productos = Producto::where('status', 1)->get();
    
        return view('edit-producto', compact('mesa', 'producto', 'productos'));
    }

    public function update($mesa_id, $id, Request $request)
    {
    $mesa = Table::findOrFail($mesa_id);
    $product = Producto::findOrFail($request->input('producto_id'));

    $producto = ElementTable::findOrFail($id);
    // Validar y actualizar los datos del producto si es necesario
    $producto->fill($request->all());
    $producto->price = $product->price;
    $producto->save();

    return redirect()->route('productos.edit', ['mesa_id' => $mesa_id, 'id' => $id])
                     ->with('success', 'Producto actualizado exitosamente.');
    }


    public function delete($mesa_id, $id)
    {
    $producto = ElementTable::findOrFail($id);
    $producto->delete();

    return redirect()->route('mesa.store',$mesa_id)->with('success', 'El producto se ha eliminado exitosamente.');
    }
}