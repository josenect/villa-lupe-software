<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Table; 
use App\Models\Producto; 
use App\Models\ElementTable;
use App\Models\DetalleFactura;


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
                $subtotalProducto = ($producto->price - $producto->dicount) * $producto->amount;
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
                return redirect()->route('mesas')->with('success', 'No Se Puede Generar Facturas y la mesa no tiene una factura antigua');
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
                $subtotalProducto = ($producto->price - $producto->dicount) * $producto->amount;
                $subtotal += $subtotalProducto;
            
                // Calcula el descuento total
                $descuentoTotalProducto = $producto->dicount * $producto->amount;
                $descuentoTotal += $descuentoTotalProducto;
            }
            
            // Calcula el gran total
            $total = $subtotal - $descuentoTotal;
            return view('pdf.detalle-factura', compact('factura','mesa', 'productosFactura', 'subtotal','descuentoTotal', 'total'))->render();
    
        }
        return redirect()->route('mesas')->with('success', 'La factura no Existe.');

    }

    
}