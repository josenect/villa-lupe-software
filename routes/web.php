<?php

use App\Models\Table;
use App\Models\ElementTable;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionsTableController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\MeseroPedidosController;
use App\Models\Producto;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== RUTAS PÚBLICAS ====================

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== RUTAS PROTEGIDAS (requieren login) ====================

Route::middleware(['role'])->group(function () {

    // Página de inicio (admin y mesero)
    Route::get('/', function () {
        $tables = Table::where('status',1)->get();
        foreach ($tables as $key => $value) {
            $tables[$key]->status = ElementTable::where('status',1)
                ->where('table_id',$value->id)
                ->where('estado', '!=', 'cancelado')
                ->exists() ? 'Ocupada' : 'Libre';
        }
        $tables = $tables->sortBy([
            ['status', 'desc'],
            ['name', 'asc']
        ])->values();
        $tables = json_decode(json_encode($tables->toArray()));
        return view('all-mesas-inicio',['tables' => $tables]);
    })->name('inicio')->middleware('role:admin,mesero');

    // ==================== RUTAS PARA ADMIN Y MESERO ====================
    
    Route::middleware(['role:admin,mesero'])->group(function () {
        
        // Gestionar mesa
        Route::prefix('mesa')->group(function () {
            Route::get('{id}', [TransactionsTableController::class, 'show'])->name('mesa.show');
            Route::post('{mesa_id}/producto', [TransactionsTableController::class, 'storeInTable'])->name('add.product.table');
            Route::get('{mesa_id}/productos/{id}/edit', [TransactionsTableController::class, 'edit'])->name('show.product.table');
            Route::put('{mesa_id}/productos/{id}/edit', [TransactionsTableController::class, 'update'])->name('update.product.table');
            // Solicitar cancelación (mesero)
            Route::post('{mesa_id}/productos/{id}/solicitar-cancelacion', [TransactionsTableController::class, 'solicitarCancelacion'])->name('solicitar.cancelacion');
        });

        // Visualizar pre factura
        Route::get('visual-pdf-pre/{mesa_id}', [PdfController::class, 'visualPdf'])->name('pdf.visualPdf');
    });

    // ==================== RUTAS SOLO ADMIN ====================
    
    Route::middleware(['role:admin'])->group(function () {
        
        // Eliminar producto de mesa (solo admin)
        Route::get('mesa/{mesa_id}/productos/{id}', [TransactionsTableController::class, 'delete'])->name('delete.product.table');
        
        // Generar factura (solo admin)
        Route::get('/generar-factura/{mesaId}', [FacturaController::class, 'generarFactura']);
        
        // Gestionar mesas
        Route::prefix('admin/mesas')->group(function () {
            Route::get('', [TableController::class, 'showMesasAdmin'])->name('admin.mesas.showAll');
            Route::post('', [TableController::class, 'storeInTable'])->name('admin.mesas.storeInTable');
            Route::get('{mesa_id}', [TableController::class, 'showtable'])->name('admin.mesas.show');
            Route::post('{mesa_id}', [TableController::class, 'update'])->name('admin.mesas.update');
            Route::get('{mesa_id}/delete', [TableController::class, 'delete'])->name('admin.mesas.delete');
        });

        // Gestionar productos
        Route::prefix('admin/productos')->group(function () {
            Route::get('', [ProductController::class, 'showProductsAdmin'])->name('admin.products.showAll');
            Route::post('', [ProductController::class, 'storeInTable'])->name('admin.products.storeInTable');
            Route::get('{product_id}', [ProductController::class, 'showtable'])->name('admin.products.show');
            Route::post('{product_id}', [ProductController::class, 'update'])->name('admin.products.update');
            Route::get('{product_id}/delete', [ProductController::class, 'delete'])->name('admin.products.delete');
        });

        // Gestionar facturas y reportes
        Route::prefix('admin/facturas')->group(function () {
            Route::get('/{date}', [FacturaController::class, 'showFacturaAdmin'])->name('admin.factura.showAll');
            Route::get('/detalle/{facturaId}', [FacturaController::class, 'showDetalle'])->name('admin.factura.detalle');
            Route::get('/anular/{facturaId}', [FacturaController::class, 'showAnular'])->name('admin.factura.showAnular');
            Route::post('/anular/{facturaId}', [FacturaController::class, 'anular'])->name('admin.factura.anular');
            Route::post('/reabrir/{facturaId}', [FacturaController::class, 'reabrir'])->name('admin.factura.reabrir');
        });

        // Gestionar usuarios
        Route::prefix('admin/usuarios')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('admin.usuarios.index');
            Route::post('', [UserController::class, 'store'])->name('admin.usuarios.store');
            Route::get('{id}/edit', [UserController::class, 'edit'])->name('admin.usuarios.edit');
            Route::put('{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
            Route::post('{id}/toggle', [UserController::class, 'toggleActivo'])->name('admin.usuarios.toggle');
            Route::delete('{id}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');
        });

        // Cancelaciones pendientes
        Route::get('admin/cancelaciones', [TransactionsTableController::class, 'cancelacionesPendientes'])->name('admin.cancelaciones.pendientes');
        Route::post('admin/cancelaciones/{id}/aprobar', [TransactionsTableController::class, 'aprobarCancelacion'])->name('admin.cancelacion.aprobar');
        Route::post('admin/cancelaciones/{id}/rechazar', [TransactionsTableController::class, 'rechazarCancelacion'])->name('admin.cancelacion.rechazar');

        // Pedidos por mesero (admin)
        Route::get('admin/pedidos-meseros', [App\Http\Controllers\AdminPedidosController::class, 'index'])->name('admin.pedidos.meseros');

        // Reporte en ticket
        Route::get('visual-reporte/{date}', [FacturaController::class, 'visualReporteTicket'])->name('admin.reporte.ticket');
    });

    // ==================== RUTAS PARA COCINA ====================
    
    Route::middleware(['role:admin,cocina'])->group(function () {
        Route::get('/cocina', [CocinaController::class, 'index'])->name('cocina.index');
        Route::get('/cocina/pedidos', [CocinaController::class, 'getPedidosAjax'])->name('cocina.pedidos');
        Route::post('/cocina/{id}/listo', [CocinaController::class, 'marcarListo'])->name('cocina.listo');
        Route::post('/cocina/{id}/en-cocina', [CocinaController::class, 'marcarEnCocina'])->name('cocina.enCocina');
    });

    // ==================== RUTAS PARA MESERO (Mis Pedidos) ====================
    
    Route::middleware(['role:admin,mesero'])->group(function () {
        Route::get('/mesero/pedidos', [MeseroPedidosController::class, 'index'])->name('mesero.pedidos');
        Route::get('/mesero/pedidos/ajax', [MeseroPedidosController::class, 'getPedidosAjax'])->name('mesero.pedidos.ajax');
        Route::post('/mesero/pedidos/{id}/entregado', [MeseroPedidosController::class, 'marcarEntregado'])->name('mesero.pedido.entregado');
    });

});

// Visualizar factura (público para impresión)
Route::get('visual-factura/{factura}', [FacturaController::class, 'visualFactura'])->name('factura.visual');

// Generar pdf preliminar
Route::get('generar-pdf-pre/{mesa_id}', [PdfController::class, 'generarPdf'])->name('pdf.generar');