<?php

use App\Models\Table;
use App\Http\Controllers\TableController;
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
    return view('welcome');
});


Route::get('/inicio', function () {
   
    $tables = Table::all()->where('status',1);
    $tables = json_decode(json_encode($tables->toArray()));
    return view('inicio',['tables' => $tables]);
});

Route::get('/mesas', function () {
    $tables = Table::all()->where('status',1);
    $tables = json_decode(json_encode($tables->toArray()));
    return $tables;
});

/*Route::get('/mesa/{id}', function (string $id) {
    $table = Table::find($id);
    if($table){
        $table = json_decode(json_encode($table->toArray()));
    }else {
        $table = [];
        $table['name'] = 'Mesa no existe';
        $table = json_decode(json_encode($table) );
    }


    return view('mesa',['data' => $table]);
   
});
*/


// Rutas para las mesas
Route::prefix('mesa')->group(function () {
    Route::get('{id}', [TableController::class, 'show'])->name('mesa.show');
});


// Ruta para almacenar un producto en una mesa
Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');