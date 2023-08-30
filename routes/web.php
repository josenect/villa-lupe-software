<?php

use App\Models\Table;
use App\Models\ElementTable;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PdfController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('inicio');
});


Route::get('/inicio', function () {
    $tables = Table::all()->where('status',1);
    foreach ($tables as $key => $value) {
        $tables[$key]->status = ElementTable::all()->where('status',1)->where('table_id',$value->id)->isEmpty() ? 'Libre' : 'Ocupada';
        # code...
    }
    $tables = json_decode(json_encode($tables->toArray()));
    return view('inicio',['tables' => $tables]);
});


// Rutas para las mesas
Route::prefix('mesa')->group(function () {
    Route::get('{id}', [TableController::class, 'show'])->name('mesa.show');
    Route::post('{id}', [TableController::class, 'show'])->name('mesa.store');

});


// Ruta para almacenar un producto en una mesa
Route::post('productos', [ProductoController::class, 'storeInTable'])->name('productos.storeInTable');
Route::get('mesa/{mesa_id}/productos/{id}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('mesa/{mesa_id}/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::get('mesa/{mesa_id}/productos/{id}', [ProductoController::class, 'delete'])->name('productos.delete');

//generar pdf preliminar 

Route::get('generar-pdf-pre/{mesa_id}', [PdfController::class, 'generarPdf'])->name('pdf.generar');
Route::get('visual-pdf-pre/{mesa_id}', [PdfController::class, 'visualPdf'])->name('pdf.visualPdf');
