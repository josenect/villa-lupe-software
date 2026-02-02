<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Table; 
use App\Models\Producto; 
use App\Models\ElementTable;
use App\Models\DetalleFactura;
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
    public function showFacturaAdmin($date)
    {
        date_default_timezone_set('America/Bogota'); 

        // Solo facturas activas para los reportes
        $facturas = Factura::whereDate('created_at', $date)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->pluck('id')->toArray();
        
        $totalProductos = 0;
        $totalPrecio = 0; 
        $detalleCocina = [];
        $cocinaTotalProductos = 0;
        $cocinaTotalPrecio = 0;
        $detalleCocinaAlmu = [];
        $cocinaTotalProductosAlmu = 0;
        $cocinaTotalPrecioAlmu = 0;
        
        $detalleElementos = DetalleFactura::select('producto_id', 'productos.name','productos.category','detalle_facturas.discount as descuento','detalle_facturas.price as precio', DB::raw('SUM(detalle_facturas.amount) as cantidad'))
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->whereIn('facturas.id', $facturas)
            ->where('facturas.estado', Factura::ESTADO_ACTIVA)
            ->groupBy('producto_id', 'productos.name','detalle_facturas.price','category','detalle_facturas.discount')
            ->get();
       
        if($detalleElementos){
            foreach ($detalleElementos as $key => $value) {
                $totalProductos = $value->cantidad + $totalProductos ;
                $totalPrecio = ($value->cantidad * ($value->precio - $value->descuento )) +  $totalPrecio ;
                if($value->category === 'restaurante-bebida' || $value->category === 'restaurante-almuerzos'){
                    $detalleCocina[]=$value;
                    $cocinaTotalProductos = $value->cantidad + $cocinaTotalProductos ;
                    $cocinaTotalPrecio = ($value->cantidad * $value->precio ) +  $cocinaTotalPrecio ;
                }
                if($value->category === 'restaurante-almuerzos'){
                    $detalleCocinaAlmu[]=$value;
                    $cocinaTotalProductosAlmu = $value->cantidad + $cocinaTotalProductosAlmu ;
                    $cocinaTotalPrecioAlmu = ($value->cantidad * $value->precio ) +  $cocinaTotalPrecioAlmu ;
                }
            }

            // Todas las facturas del día (activas y anuladas) para mostrar en la lista
            $facturas = Factura::with('mesa')
                ->whereDate('created_at', $date)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $facturasTotal = 0;
            $totalEfectivo = 0;
            $totalTransferencia = 0;
            $propinaTotal = 0;
            
            foreach ($facturas as $key => $value) {
                // Solo sumar las activas al total
                if($value->estado === Factura::ESTADO_ACTIVA) {
                    $facturasTotal = $facturasTotal + $value->valor_pagado;
                    $propinaTotal = $propinaTotal + $value->valor_propina;
                    
                    // Sumar por los campos de valor
                    $totalEfectivo = $totalEfectivo + $value->valor_efectivo;
                    $totalTransferencia = $totalTransferencia + $value->valor_transferencia;
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
                'detalleCocina',
                'cocinaTotalProductos',
                'cocinaTotalPrecio',
                'detalleCocinaAlmu',
                'cocinaTotalProductosAlmu',
                'cocinaTotalPrecioAlmu',
                'date'
            ))->render();
        }
        
        return redirect()->route('inicio')->with('success', 'Las facturas no existen.');
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
            
            // 2. Marcar la factura como reabierta/anulada
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
     * Visualizar reporte del día en formato ticket (44mm)
     */
    public function visualReporteTicket($date, Request $request)
    {
        date_default_timezone_set('America/Bogota'); 
        
        $tipo = $request->get('data', 'facturas');

        // Solo facturas activas para los reportes
        $facturasIds = Factura::whereDate('created_at', $date)
            ->where('estado', Factura::ESTADO_ACTIVA)
            ->pluck('id')->toArray();
        
        $totalProductos = 0;
        $totalPrecio = 0; 
        $detalleCocina = [];
        $cocinaTotalProductos = 0;
        $cocinaTotalPrecio = 0;
        $detalleCocinaAlmu = [];
        $cocinaTotalProductosAlmu = 0;
        $cocinaTotalPrecioAlmu = 0;
        
        $detalleElementos = DetalleFactura::select('producto_id', 'productos.name','productos.category','detalle_facturas.discount as descuento','detalle_facturas.price as precio', DB::raw('SUM(detalle_facturas.amount) as cantidad'))
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->whereIn('facturas.id', $facturasIds)
            ->where('facturas.estado', Factura::ESTADO_ACTIVA)
            ->groupBy('producto_id', 'productos.name','detalle_facturas.price','category','detalle_facturas.discount')
            ->get();
       
        foreach ($detalleElementos as $key => $value) {
            $totalProductos = $value->cantidad + $totalProductos ;
            $totalPrecio = ($value->cantidad * ($value->precio - $value->descuento )) +  $totalPrecio ;
            if($value->category === 'restaurante-bebida' || $value->category === 'restaurante-almuerzos'){
                $detalleCocina[]=$value;
                $cocinaTotalProductos = $value->cantidad + $cocinaTotalProductos ;
                $cocinaTotalPrecio = ($value->cantidad * $value->precio ) +  $cocinaTotalPrecio ;
            }
            if($value->category === 'restaurante-almuerzos'){
                $detalleCocinaAlmu[]=$value;
                $cocinaTotalProductosAlmu = $value->cantidad + $cocinaTotalProductosAlmu ;
                $cocinaTotalPrecioAlmu = ($value->cantidad * $value->precio ) +  $cocinaTotalPrecioAlmu ;
            }
        }

        // Todas las facturas del día (activas y anuladas) para mostrar en la lista
        $facturas = Factura::with('mesa')
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $facturasTotal = 0;
        $totalEfectivo = 0;
        $totalTransferencia = 0;
        $propinaTotal = 0;
        
        foreach ($facturas as $key => $value) {
            if($value->estado === Factura::ESTADO_ACTIVA) {
                $facturasTotal = $facturasTotal + $value->valor_pagado;
                $propinaTotal = $propinaTotal + $value->valor_propina;
                
                // Sumar por los campos de valor
                $totalEfectivo = $totalEfectivo + $value->valor_efectivo;
                $totalTransferencia = $totalTransferencia + $value->valor_transferencia;
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
            'detalleCocina',
            'cocinaTotalProductos',
            'cocinaTotalPrecio',
            'detalleCocinaAlmu',
            'cocinaTotalProductosAlmu',
            'cocinaTotalPrecioAlmu',
            'date',
            'tipo'
        ))->render();
    }
}
