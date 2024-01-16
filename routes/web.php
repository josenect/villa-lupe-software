<?php

use App\Models\Table;
use App\Models\ElementTable;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TransactionsTableController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\FacturaController;


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
    //mostras mesas disponibles y ocupadas
    $tables = Table::all()->where('status',1);
    foreach ($tables as $key => $value) {
        $tables[$key]->status = ElementTable::all()->where('status',1)->where('table_id',$value->id)->isEmpty() ? 'Libre' : 'Ocupada';
        # code...
    }
    $tables = json_decode(json_encode($tables->toArray()));
    return view('all-mesas-inicio',['tables' => $tables]);
});


// Rutas para gestionar mesa
Route::prefix('mesa')->group(function () {
    //listar informacion mesa
    Route::get('{id}', [TransactionsTableController::class, 'show'])->name('mesa.show');
    //agregar producto mesa
    Route::post('{mesa_id}/producto', [TransactionsTableController::class, 'storeInTable'])->name('add.product.table');
    //mostrar producto ha editar en mesa
    Route::get('{mesa_id}/productos/{id}/edit', [TransactionsTableController::class, 'edit'])->name('show.product.table');
    //editar producto en mesa
    Route::put('{mesa_id}/productos/{id}/edit', [TransactionsTableController::class, 'update'])->name('update.product.table');
    //eliminar producto de mesa
    Route::get('{mesa_id}/productos/{id}', [TransactionsTableController::class, 'delete'])->name('delete.product.table');
});

//Visualizar pre factura 
Route::get('visual-pdf-pre/{mesa_id}', [PdfController::class, 'visualPdf'])->name('pdf.visualPdf');
//Generar factura 
Route::get('/generar-factura/{mesaId}',[FacturaController::class, 'generarFactura']);
//Visualizar factura
Route::get('visual-factura/{factura}', [FacturaController::class, 'visualFactura'])->name('factura.visual');

//Generar pdf preliminar en prueba sale mal impresion
Route::get('generar-pdf-pre/{mesa_id}', [PdfController::class, 'generarPdf'])->name('pdf.generar');

//Gestionar mesas admin
Route::prefix('admin/mesas')->group(function () {
    //mostrar mesas 
    Route::get('', [TableController::class, 'showMesasAdmin'])->name('admin.mesas.showAll');
    //agregar nuevas mesas
    Route::post('', [TableController::class, 'storeInTable'])->name('admin.mesas.storeInTable');
    //mostrar informacion mesa para actualizar
    Route::get('{mesa_id}', [TableController::class, 'showtable'])->name('admin.mesas.show');
    //actualizar informacion mesa
    Route::post('{mesa_id}', [TableController::class, 'update'])->name('admin.mesas.update');
    //eliminar producto de mesa
    Route::get('{mesa_id}/delete', [TableController::class, 'delete'])->name('admin.mesas.delete');
});