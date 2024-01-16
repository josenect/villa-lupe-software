<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Producto;
use App\Models\ElementTable;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TableController extends Controller
{

    //mostrar mesas admin
    public function showMesasAdmin()
    {
        $tables = Table::orderBy('name', 'ASC')->orderBy('status', 'desc')->get();
        return view('mesas-admin', compact('tables'));
    }

    //crear mesa admin
    public function storeInTable(Request $request)
    {
        // Validar los datos recibidos del formulario (si es necesario).
        $request->validate([
            'name' => 'required',
            'location' => 'required',
        ]);
   
        $table = Table::where('name',$request->input('name'))->where('location',$request->input('location'))->get();

        if((!$table->isEmpty())){

            return redirect()->route('admin.mesas.show')->with('error', 'Existe una mesa con el mismo nombre en la ubicacion.');

        }
        date_default_timezone_set('America/Bogota');
        // Crear un nuevo registro en la tabla 'productos'.
        $Table = new Table();
        $Table->name = $request->input('name');
        $Table->location = $request->input('location');
        $Table->status = true;
        $Table->save();
        // Puedes agregar algún mensaje de éxito o redireccionar a otra página después de guardar el producto.
        return redirect()->route('admin.mesas.showAll')->with('success', 'La Mesa ha sido registrada exitosamente.');
    }

    //edit table admin
    public function showtable($mesa_id)
    {
        $table = Table::findOrFail($mesa_id);
        return view('mesas-admin-edit', compact('table'));
    }

    // update table admin
    public function update($mesa_id, Request $request)
    {
        // Validar los datos recibidos del formulario (si es necesario).
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'status' => 'required'
        ]);
        $table = Table::where('name',$request->input('name'))->where('location',$request->input('location'))->get();

        if((!$table->isEmpty())){
            foreach ($table as $key => $value) {
                if($value->id == $mesa_id){
                    continue;
                }else{
                    return redirect()->route('admin.mesas.showAll')->with('error', 'Existe una mesa con el mismo nombre en la ubicacion.');
                }
            }
        }

        if($request->input('status') == 0){
            $productosTable = ElementTable::with('producto')
            ->where('status', 1)
            ->where('table_id', $mesa_id)
            ->get();
    
            if($productosTable->count() > 0){
                return redirect()->route('admin.mesas.showAll')->with('error', 'La Mesa tiene productos cargados, No se puede desactivar');
        
            }

        }
        $Table = Table::findOrFail($mesa_id);
        // Validar y actualizar los datos del producto si es necesario
        $Table->fill($request->all());
        $Table->save();

        return redirect()->route('admin.mesas.showAll')->with('success', 'La Mesa ha sido actualizada exitosamente.');
    }

    //delete admin table admin
    public function delete($mesa_id)
    {
        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('table_id', $mesa_id)
        ->get();

        if($productosTable->count() > 0){
            return redirect()->route('admin.mesas.showAll')->with('error', 'La Mesa tiene productos cargados, No se puede eliminar');

        }else{

            $table = Table::findOrFail($mesa_id);
            $table->delete();
            return redirect()->route('admin.mesas.showAll')->with('success', 'La Mesa ha sido eliminada.');
        }

    }
}


