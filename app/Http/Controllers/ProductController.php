<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ElementTable;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ProductController extends Controller
{

    //mostrar mesas admin
    public function showProductsAdmin()
    {
        $products = Producto::orderBy('name', 'ASC')->orderBy('status', 'desc')->get();
        return view('products-admin', compact('products'));
    }

    //crear mesa admin
    public function storeInTable(Request $request)
    {
        // Validar los datos recibidos del formulario (si es necesario).
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required',
            'inventory' => 'required',
        ]);
   
        //$table = Producto::where('name',$request->input('name'))->where('location',$request->input('location'))->get();

       // if((!$table->isEmpty())){

        //    return redirect()->route('admin.mesas.show')->with('error', 'Existe una mesa con el mismo nombre en la ubicacion.');

       // }

        date_default_timezone_set('America/Bogota');
        // Crear un nuevo registro en la tabla 'productos'.
        $Table = new Producto();
        $Table->name = $request->input('name');
        $Table->category = $request->input('category');
        $Table->price = $request->input('price');
        $Table->inventory = $request->input('inventory');

        $Table->status = true;
        $Table->save();
        // Puedes agregar algún mensaje de éxito o redireccionar a otra página después de guardar el producto.
        return redirect()->route('admin.products.showAll')->with('success', 'El Producto ha sido creado exitosamente.');
    }

    //edit table admin
    public function showtable($mesa_id)
    {
        $product = Producto::findOrFail($mesa_id);
        return view('products-admin-edit', compact('product'));
    }

    // update table admin
    public function update($product_id, Request $request)
    {
        // Validar los datos recibidos del formulario (si es necesario).
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required',
            'inventory' => 'required',
            'status' => 'required'
        ]);

        if($request->input('status') === 0){
            $productosTable = ElementTable::with('producto')
            ->where('status', 1)
            ->where('producto_id', $product_id)
            ->get();
    
            if($productosTable->count() > 0){
                return redirect()->route('admin.products.showAll')->with('error', 'El producto esta cargado en las mesas, No se puede desactivar el producto');
    
            }

        }
       

        $Table = Producto::findOrFail($product_id);
        // Validar y actualizar los datos del producto si es necesario
        $Table->fill($request->all());
        $Table->save();

        return redirect()->route('admin.products.showAll')->with('success', 'El producto ha sido actualizado exitosamente.');
    }

    //delete admin table admin
    public function delete($product_id)
    {
        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('producto_id', $product_id)
        ->get();

        if($productosTable->count() > 0){
            return redirect()->route('admin.mesas.showAll')->with('error', 'El producto esta cargado en las mesas, No se puede eliminar');

        }else{

            $table = Producto::findOrFail($product_id);
            $table->delete();
            return redirect()->route('admin.products.showAll')->with('success', 'El Producto ha sido eliminado.');
        }

    }
}


