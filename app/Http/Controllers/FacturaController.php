<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Table;
use App\Models\Producto;
use App\Models\ElementTable;
use App\Models\DetalleFactura;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class FacturaController extends Controller
{
    //generar factura 
    public function generarFactura($mesaId, Request $request)
    {
        date_default_timezone_set('America/Bogota'); 

        $propina = $request->input('propina') ?? 0;
        $medioPago = $request->input('medio_pago') ?? 'Efectivo';
        $valorEfectivo = $request->input('efectivo') ?? 0;
        $valorTransferencia = $request->input('transferencia') ?? 0;

        $productosTable = ElementTable::with('producto')
        ->where('status', 1)
        ->where('table_id', $mesaId)
        ->get();

        if($productosTable->count() > 0){
            $subtotal = 0;
            $descuentoTotal = 0;
            foreach ($productosTable as $producto) {
                // Calcula el subtotal para cada producto
                $subtotalProducto = $producto->price * $producto->amount;
                $subtotal += $subtotalProducto;
                // Calcula el descuento total
                $descuentoTotalProducto = $producto->dicount * $producto->amount;
                $descuentoTotal += $descuentoTotalProducto;
    
                // Marcar como facturado (no eliminar para mantener historial)
                $producto->status = 0;
                $producto->save();
    
            }
            
            // Calcula el gran total
            $total = $subtotal - $descuentoTotal;
            
            // Genera un número de factura único
            $numeroFactura = 'F' . str_pad(Factura::max('id') + 1, 3, '0', STR_PAD_LEFT);
    
            $factura = new Factura;
            $factura->numero_factura = $numeroFactura ;
            $factura->valor_total = $total;
            $factura->table_id = $mesaId;
            $factura->user_id = Auth::id();
            $factura->valor_propina = $propina;
            $factura->valor_pagado = $total + $propina;
            $factura->valor_efectivo = $valorEfectivo;
            $factura->valor_transferencia = $valorTransferencia;
            $factura->fecha_hora_factura = now();
            $factura->medio_pago = $medioPago;
            $factura->estado = Factura::ESTADO_ACTIVA;
            $factura->save();
    
            foreach ($productosTable as $key => $value) {
                $facturaDeTalle = new DetalleFactura;
                $facturaDeTalle->table_id = $value->table_id;
                $facturaDeTalle->factura_id = $factura->id;
                $facturaDeTalle->producto_id = $value->producto_id;
                $facturaDeTalle->price = $value->price;
                $facturaDeTalle->amount = $value->amount;
                $facturaDeTalle->discount = $value->dicount;
                $facturaDeTalle->record = $value->record;

                $facturaDeTalle->save();
            }

            // Liberar occupied_at al facturar la mesa
            Table::where('id', $mesaId)->update(['occupied_at' => null]);

        }else{
            $ultimaFactura = Factura::where('table_id', $mesaId)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->latest()
            ->first();
            if(!is_null($ultimaFactura)){
              $numeroFactura  = $ultimaFactura->numero_factura;
            }else{
                return redirect()->route('inicio')->with('success', 'No Se Puede Generar Facturas y la mesa no tiene una factura antigua');
            }
        }


        return redirect()->route('factura.visual',$numeroFactura);
    }

    //visualizar factura
    public function visualFactura($factura)
    {
        date_default_timezone_set('America/Bogota'); 
        $factura = Factura::where('numero_factura', $factura)
                  ->first();
        if(!is_null($factura)){
            $mesa = Table::findOrFail($factura->table_id);
            $productosFactura = DetalleFactura::with('factura','producto')
            ->where('factura_id', $factura->id)
            ->get();
    
            $subtotal = 0;
            $descuentoTotal = 0;
            foreach ($productosFactura as $producto) {
                // Calcula el subtotal para cada producto
                $subtotalProducto = $producto->price * $producto->amount;
                $subtotal += $subtotalProducto;
            
                // Calcula el descuento total
                $descuentoTotalProducto = $producto->discount * $producto->amount;
                $descuentoTotal += $descuentoTotalProducto;
            }
            
            // Calcula el gran total
            $total = $subtotal - $descuentoTotal;
            return view('pdf.detalle-factura', compact('factura','mesa', 'productosFactura', 'subtotal','descuentoTotal', 'total'))->render();
    
        }
        return redirect()->route('inicio')->with('success', 'La factura no Existe.');

    }

    //visualizar factura admin con reportes del día
    public function showFacturaAdmin($date, Request $request)
    {
        date_default_timezone_set('America/Bogota');

        $desde = $date;
        $hasta = $request->get('hasta', $date);

        $facturasIds = Factura::whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->pluck('id')->toArray();

        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        // Inicializar estructura dinámica por categoría
        $categoriaData = [];
        foreach ($categorias as $cat) {
            $categoriaData[$cat->slug] = [
                'nombre'         => $cat->nombre,
                'es_cocina'      => $cat->es_cocina,
                'productos'      => [],
                'totalProductos' => 0,
                'totalPrecio'    => 0,
            ];
        }
        $cocinaTodo = ['productos' => [], 'totalProductos' => 0, 'totalPrecio' => 0];

        $totalProductos = 0;
        $totalPrecio    = 0;

        $detalleElementos = DetalleFactura::select(
                'producto_id',
                'productos.name',
                'productos.category',
                'detalle_facturas.discount as descuento',
                'detalle_facturas.price as precio',
                DB::raw('SUM(detalle_facturas.amount) as cantidad')
            )
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->whereIn('facturas.id', $facturasIds)
            ->where('facturas.estado', Factura::ESTADO_ACTIVA)
            ->groupBy('producto_id', 'productos.name', 'detalle_facturas.price', 'category', 'detalle_facturas.discount')
            ->get();

        foreach ($detalleElementos as $value) {
            $totalProductos += $value->cantidad;
            $totalPrecio    += $value->cantidad * ($value->precio - $value->descuento);

            if (isset($categoriaData[$value->category])) {
                $categoriaData[$value->category]['productos'][]       = $value;
                $categoriaData[$value->category]['totalProductos']   += $value->cantidad;
                $categoriaData[$value->category]['totalPrecio']      += $value->cantidad * ($value->precio - $value->descuento);

                if ($categoriaData[$value->category]['es_cocina']) {
                    $cocinaTodo['productos'][]     = $value;
                    $cocinaTodo['totalProductos'] += $value->cantidad;
                    $cocinaTodo['totalPrecio']    += $value->cantidad * ($value->precio - $value->descuento);
                }
            }
        }

        // Todas las facturas del período para mostrar en lista
        $facturas = Factura::with('mesa')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc')
            ->get();

        $facturasTotal      = 0;
        $totalEfectivo      = 0;
        $totalTransferencia = 0;
        $propinaTotal       = 0;

        foreach ($facturas as $value) {
            if ($value->estado === Factura::ESTADO_ACTIVA) {
                $facturasTotal      += $value->valor_pagado;
                $propinaTotal       += $value->valor_propina;
                $totalEfectivo      += $value->valor_efectivo;
                $totalTransferencia += $value->valor_transferencia;
            }
        }

        return view('pdf.detalle-factura-day', compact(
            'facturas',
            'detalleElementos',
            'totalProductos',
            'totalPrecio',
            'facturasTotal',
            'totalEfectivo',
            'totalTransferencia',
            'propinaTotal',
            'categorias',
            'categoriaData',
            'cocinaTodo',
            'date',
            'desde',
            'hasta'
        ))->render();
    }

    /**
     * Mostrar formulario para anular factura
     */
    public function showAnular($facturaId)
    {
        $factura = Factura::with('mesa', 'detalleFacturas.producto')->findOrFail($facturaId);
        
        if($factura->estado !== Factura::ESTADO_ACTIVA) {
            return redirect()->back()->with('error', 'Esta factura ya está anulada o no se puede modificar.');
        }
        
        return view('facturas.anular', compact('factura'));
    }

    /**
     * Anular una factura
     */
    public function anular(Request $request, $facturaId)
    {
        date_default_timezone_set('America/Bogota');
        
        $factura = Factura::findOrFail($facturaId);
        
        if($factura->estado !== Factura::ESTADO_ACTIVA) {
            return redirect()->back()->with('error', 'Esta factura ya está anulada.');
        }
        
        $factura->estado = Factura::ESTADO_ANULADA;
        $factura->motivo_anulacion = $request->input('motivo', 'Sin motivo especificado');
        $factura->fecha_anulacion = now();
        $factura->save();
        
        return redirect()->route('admin.factura.showAll', date('Y-m-d'))
            ->with('success', 'Factura ' . $factura->numero_factura . ' anulada correctamente.');
    }

    /**
     * Reabrir una factura (anularla y cargar productos a la mesa)
     */
    public function reabrir(Request $request, $facturaId)
    {
        date_default_timezone_set('America/Bogota');
        
        $factura = Factura::with('detalleFacturas')->findOrFail($facturaId);
        
        if($factura->estado !== Factura::ESTADO_ACTIVA) {
            return redirect()->back()->with('error', 'Esta factura ya está anulada y no se puede reabrir.');
        }
        
        DB::beginTransaction();
        
        try {
            // 1. Cargar los productos de la factura a la mesa (ya entregados, solo para facturar)
            foreach ($factura->detalleFacturas as $detalle) {
                $elementTable = new ElementTable();
                $elementTable->table_id = $factura->table_id;
                $elementTable->producto_id = $detalle->producto_id;
                $elementTable->price = $detalle->price;
                $elementTable->amount = $detalle->amount;
                $elementTable->dicount = $detalle->discount;
                $elementTable->record = now();
                $elementTable->status = 1;
                $elementTable->estado = ElementTable::ESTADO_ENTREGADO; // Ya fueron entregados, no van a cocina
                $elementTable->save();
            }
            
            // 2. Restaurar occupied_at en la mesa al reabrir
            Table::where('id', $factura->table_id)->whereNull('occupied_at')->update(['occupied_at' => now()]);

            // 3. Marcar la factura como reabierta/anulada
            $factura->estado = Factura::ESTADO_REABIERTA;
            $factura->motivo_anulacion = $request->input('motivo', 'Factura reabierta - productos cargados a la mesa');
            $factura->fecha_anulacion = now();
            $factura->save();
            
            DB::commit();
            
            return redirect()->route('mesa.show', $factura->table_id)
                ->with('success', 'Factura ' . $factura->numero_factura . ' reabierta. Los productos han sido cargados a la mesa.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al reabrir la factura: ' . $e->getMessage());
        }
    }

    /**
     * Exportar el informe activo a CSV (facturas, productos, cocina, o categoría)
     */
    public function exportarCSV($date, Request $request)
    {
        date_default_timezone_set('America/Bogota');

        $desde = $date;
        $hasta = $request->get('hasta', $date);
        $data  = $request->get('data', 'facturas');

        $esCat      = str_starts_with($data, 'cat-');
        $esCocina   = $data === 'cocina';
        $esFacturas = $data === 'facturas';
        $slug       = $esCat ? substr($data, 4) : null;

        $facturasIds = Factura::whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->pluck('id')->toArray();

        // ── Facturas ──────────────────────────────────────────────────────────
        if ($esFacturas) {
            $rows = Factura::with('mesa', 'usuario')
                ->whereIn('id', $facturasIds)
                ->orderBy('created_at', 'asc')
                ->get();

            $filename = 'facturas-' . $desde . ($desde !== $hasta ? '-a-' . $hasta : '') . '.csv';
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['Folio', 'Mesa', 'Mesero', 'Fecha', 'Hora', 'Subtotal', 'Propina', 'Total', 'Medio Pago']);
                foreach ($rows as $f) {
                    fputcsv($out, [
                        $f->numero_factura,
                        $f->mesa->name    ?? '—',
                        $f->usuario->name ?? '—',
                        $f->created_at->format('Y-m-d'),
                        $f->created_at->format('H:i'),
                        $f->valor_total,
                        $f->valor_propina,
                        $f->valor_pagado,
                        $f->medio_pago ?? 'Efectivo',
                    ]);
                }
                fclose($out);
            };

        // ── Productos (todos) ─────────────────────────────────────────────────
        } elseif ($data === 'productos') {
            $rows = DetalleFactura::select(
                    'productos.name',
                    'productos.category',
                    DB::raw('SUM(detalle_facturas.amount) as cantidad'),
                    'detalle_facturas.price as precio',
                    'detalle_facturas.discount as descuento'
                )
                ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
                ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
                ->whereIn('facturas.id', $facturasIds)
                ->groupBy('productos.name', 'productos.category', 'detalle_facturas.price', 'detalle_facturas.discount')
                ->orderByDesc('cantidad')
                ->get();

            $filename = 'productos-' . $desde . ($desde !== $hasta ? '-a-' . $hasta : '') . '.csv';
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['Producto', 'Categoría', 'Cantidad', 'Precio Unit.', 'Total']);
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->name,
                        $p->category,
                        $p->cantidad,
                        $p->precio - $p->descuento,
                        ($p->precio - $p->descuento) * $p->cantidad,
                    ]);
                }
                fclose($out);
            };

        // ── Cocina (todas las categorías es_cocina) ───────────────────────────
        } elseif ($esCocina) {
            $slugsCocina = Categoria::where('es_cocina', true)->where('activo', true)->pluck('slug')->toArray();

            $rows = DetalleFactura::select(
                    'productos.name',
                    'productos.category',
                    DB::raw('SUM(detalle_facturas.amount) as cantidad'),
                    'detalle_facturas.price as precio',
                    'detalle_facturas.discount as descuento'
                )
                ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
                ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
                ->whereIn('facturas.id', $facturasIds)
                ->whereIn('productos.category', $slugsCocina)
                ->groupBy('productos.name', 'productos.category', 'detalle_facturas.price', 'detalle_facturas.discount')
                ->orderByDesc('cantidad')
                ->get();

            $filename = 'cocina-' . $desde . ($desde !== $hasta ? '-a-' . $hasta : '') . '.csv';
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['Producto', 'Categoría', 'Cantidad', 'Precio Unit.', 'Total']);
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->name,
                        $p->category,
                        $p->cantidad,
                        $p->precio - $p->descuento,
                        ($p->precio - $p->descuento) * $p->cantidad,
                    ]);
                }
                fclose($out);
            };

        // ── Categoría específica ──────────────────────────────────────────────
        } elseif ($esCat && $slug) {
            $rows = DetalleFactura::select(
                    'productos.name',
                    'productos.category',
                    DB::raw('SUM(detalle_facturas.amount) as cantidad'),
                    'detalle_facturas.price as precio',
                    'detalle_facturas.discount as descuento'
                )
                ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
                ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
                ->whereIn('facturas.id', $facturasIds)
                ->where('productos.category', $slug)
                ->groupBy('productos.name', 'productos.category', 'detalle_facturas.price', 'detalle_facturas.discount')
                ->orderByDesc('cantidad')
                ->get();

            $filename = 'categoria-' . $slug . '-' . $desde . ($desde !== $hasta ? '-a-' . $hasta : '') . '.csv';
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['Producto', 'Cantidad', 'Precio Unit.', 'Total']);
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->name,
                        $p->cantidad,
                        $p->precio - $p->descuento,
                        ($p->precio - $p->descuento) * $p->cantidad,
                    ]);
                }
                fclose($out);
            };

        } else {
            return redirect()->back();
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ver detalle de una factura específica
     */
    public function showDetalle($facturaId)
    {
        $factura = Factura::with('mesa', 'detalleFacturas.producto')->findOrFail($facturaId);
        
        $subtotal = 0;
        $descuentoTotal = 0;
        
        foreach ($factura->detalleFacturas as $detalle) {
            $subtotal += $detalle->price * $detalle->amount;
            $descuentoTotal += $detalle->discount * $detalle->amount;
        }
        
        $total = $subtotal - $descuentoTotal;
        
        return view('facturas.detalle', compact('factura', 'subtotal', 'descuentoTotal', 'total'));
    }

    /**
     * Visualizar reporte del día en formato ticket (58mm)
     */
    public function visualReporteTicket($date, Request $request)
    {
        date_default_timezone_set('America/Bogota');

        $tipo  = $request->get('data', 'facturas');
        $desde = $date;
        $hasta = $request->get('hasta', $date);

        $facturasIds = Factura::whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->pluck('id')->toArray();

        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        $categoriaData = [];
        foreach ($categorias as $cat) {
            $categoriaData[$cat->slug] = [
                'nombre'         => $cat->nombre,
                'es_cocina'      => $cat->es_cocina,
                'productos'      => [],
                'totalProductos' => 0,
                'totalPrecio'    => 0,
            ];
        }
        $cocinaTodo = ['productos' => [], 'totalProductos' => 0, 'totalPrecio' => 0];

        $totalProductos = 0;
        $totalPrecio    = 0;

        $detalleElementos = DetalleFactura::select(
                'producto_id',
                'productos.name',
                'productos.category',
                'detalle_facturas.discount as descuento',
                'detalle_facturas.price as precio',
                DB::raw('SUM(detalle_facturas.amount) as cantidad')
            )
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->whereIn('facturas.id', $facturasIds)
            ->where('facturas.estado', Factura::ESTADO_ACTIVA)
            ->groupBy('producto_id', 'productos.name', 'detalle_facturas.price', 'category', 'detalle_facturas.discount')
            ->get();

        foreach ($detalleElementos as $value) {
            $totalProductos += $value->cantidad;
            $totalPrecio    += $value->cantidad * ($value->precio - $value->descuento);

            if (isset($categoriaData[$value->category])) {
                $categoriaData[$value->category]['productos'][]     = $value;
                $categoriaData[$value->category]['totalProductos'] += $value->cantidad;
                $categoriaData[$value->category]['totalPrecio']    += $value->cantidad * ($value->precio - $value->descuento);

                if ($categoriaData[$value->category]['es_cocina']) {
                    $cocinaTodo['productos'][]     = $value;
                    $cocinaTodo['totalProductos'] += $value->cantidad;
                    $cocinaTodo['totalPrecio']    += $value->cantidad * ($value->precio - $value->descuento);
                }
            }
        }

        $facturas = Factura::with('mesa')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc')
            ->get();

        $facturasTotal      = 0;
        $totalEfectivo      = 0;
        $totalTransferencia = 0;
        $propinaTotal       = 0;

        foreach ($facturas as $value) {
            if ($value->estado === Factura::ESTADO_ACTIVA) {
                $facturasTotal      += $value->valor_pagado;
                $propinaTotal       += $value->valor_propina;
                $totalEfectivo      += $value->valor_efectivo;
                $totalTransferencia += $value->valor_transferencia;
            }
        }

        return view('pdf.reporte-dia-ticket', compact(
            'facturas',
            'detalleElementos',
            'totalProductos',
            'totalPrecio',
            'facturasTotal',
            'totalEfectivo',
            'totalTransferencia',
            'propinaTotal',
            'categorias',
            'categoriaData',
            'cocinaTodo',
            'date',
            'desde',
            'hasta',
            'tipo'
        ))->render();
    }
}
