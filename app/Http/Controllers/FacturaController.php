<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Table; 
use App\Models\Producto; 
use App\Models\ElementTable;
use App\Models\DetalleFactura;
use Illuminate\Support\Facades\DB;


class FacturaController extends Controller
{
    //generar factura 
    public function generarFactura($mesaId, Request $request)
    {
        date_default_timezone_set('America/Bogota'); 

        $propina = $request->input('propina')?? 0;

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
    
                $producto->delete();
    
            }
            
            // Calcula el gran total
            $total = $subtotal - $descuentoTotal;
            
            // Genera un número de factura único
            $numeroFactura = 'F' . str_pad(Factura::max('id') + 1, 3, '0', STR_PAD_LEFT);
    
            $factura = new Factura;
            $factura->numero_factura = $numeroFactura ;
            $factura->valor_total = $total;
            $factura->table_id = $mesaId;
            $factura->valor_propina = $propina;
            $factura->valor_pagado = $total + $propina;
            $factura->fecha_hora_factura = now(); // Puedes usar Carbon para la fecha y hora actual
            $factura->medio_pago = 'Efectivo';
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
            ->latest() // Ordena por fecha_hora_factura de manera descendente
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
                // ... (tu código actual)
            
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

   //visualizar factura
   public function showFacturaAdmin($date)
   {
   
       date_default_timezone_set('America/Bogota'); 

        $facturas = Factura::whereDate('created_at', $date)->pluck('id')->toArray();
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
            ->groupBy('producto_id', 'productos.name','detalle_facturas.price','category','detalle_facturas.discount')
            ->get();
       if($detalleElementos ){

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

        $facturas = Factura::whereDate('created_at', $date)->get();
        $facturasTotal = 0;
        foreach ($facturas as $key => $value) {
            $facturasTotal = $facturasTotal + $value->valor_pagado ;
        }

           return view('pdf.detalle-factura-day', compact('facturas','detalleElementos','totalProductos','totalPrecio','facturas','facturasTotal','detalleCocina','cocinaTotalProductos','cocinaTotalPrecio','detalleCocinaAlmu','cocinaTotalProductosAlmu','cocinaTotalPrecioAlmu'))->render();

       }
       return redirect()->route('inicio')->with('success', 'Las facturano no Existe.');

   }
    
    
}