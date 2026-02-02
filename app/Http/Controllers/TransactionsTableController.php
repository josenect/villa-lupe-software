<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElementTable;
use App\Models\Producto; 
use App\Models\Table;
use Illuminate\Support\Facades\Auth;

class TransactionsTableController extends Controller
{
    //mostrar productos de la mesa
    public function show($id)
    {
        $mesa = Table::findOrFail($id);
        $productos = Producto::orderBy('name', 'ASC')->where('status',1)->get();
        $productosTable = ElementTable::with(['producto', 'usuario'])
            ->where('status', 1)
            ->where('table_id', $id)
            ->where('estado', '!=', ElementTable::ESTADO_CANCELADO)
            ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($productosTable as $producto) {
            // Solo sumar si no está en cancelación solicitada o cancelado
            if ($producto->estado !== ElementTable::ESTADO_CANCELACION_SOLICITADA) {
                $subtotalProducto = $producto->price * $producto->amount;
                $subtotal += $subtotalProducto;
                $descuentoTotalProducto = $producto->dicount * $producto->amount;
                $descuentoTotal += $descuentoTotalProducto;
            }
        }
        
        // Calcula el gran total
        $total = $subtotal - $descuentoTotal;
        
        return view('id-mesa', compact('mesa', 'productos','productosTable', 'subtotal','descuentoTotal', 'total'));
    }

    /**
     * Almacena un nuevo producto en la mesa.
     */
    public function storeInTable($mesa_id, Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'amount' => 'required|integer',
        ]);

        $product = Producto::findOrFail($request->input('product_id'));

        if($request->input('dicount') > $product->price){
            return redirect()->route('mesa.show',$mesa_id)->with('error', 'El Valor del Descuento es mayor al valor del Producto.');
        }

        date_default_timezone_set('America/Bogota');
        
        $productoTable = new ElementTable();
        $productoTable->price = $product->price;
        $productoTable->table_id = $mesa_id;
        $productoTable->producto_id = $request->input('product_id');
        $productoTable->amount = $request->input('amount');
        $productoTable->dicount = $request->input('dicount') ?? 0;
        $productoTable->observacion = $request->input('observacion');
        $productoTable->record = date('Y-m-d H:i:s');
        $productoTable->status = true;
        $productoTable->estado = ElementTable::ESTADO_PENDIENTE;
        $productoTable->user_id = Auth::id();
        $productoTable->save();

        return redirect()->route('mesa.show', $mesa_id)->with('success', 'El producto ha sido cargado exitosamente.');
    }

    //mostrar el producto de la tabla
    public function edit($mesa_id, $id)
    {
        $mesa = Table::findOrFail($mesa_id);
        $producto = ElementTable::findOrFail($id);
        $productos = Producto::orderBy('name', 'ASC')->where('status',1)->get();
        
        return view('id-mesa-edit-producto', compact('mesa', 'producto', 'productos'));
    }

    //actualizar el producto de la tabla
    public function update($mesa_id, $id, Request $request)
    {
        $productData = Producto::findOrFail($request->input('producto_id'));

        $producto = ElementTable::findOrFail($id);
        $producto->fill($request->all());
        $producto->price = $productData->price;
        $producto->observacion = $request->input('observacion');
        $producto->save();

        return redirect()->route('show.product.table', ['mesa_id' => $mesa_id, 'id' => $id])
                        ->with('success', 'Producto actualizado exitosamente.');
    }

    //eliminar producto de la mesa (solo admin)
    public function delete($mesa_id, $id)
    {
        $producto = ElementTable::findOrFail($id);
        $producto->delete();

        return redirect()->route('mesa.show', $mesa_id)->with('success', 'El producto se ha eliminado exitosamente.');
    }

    /**
     * Solicitar cancelación de un producto (mesero)
     */
    public function solicitarCancelacion($mesa_id, $id, Request $request)
    {
        date_default_timezone_set('America/Bogota');
        
        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $producto = ElementTable::findOrFail($id);
        
        // Verificar que no esté ya en proceso de cancelación
        if ($producto->estado === ElementTable::ESTADO_CANCELACION_SOLICITADA) {
            return redirect()->route('mesa.show', $mesa_id)->with('error', 'Este producto ya tiene una solicitud de cancelación pendiente.');
        }

        $producto->estado = ElementTable::ESTADO_CANCELACION_SOLICITADA;
        $producto->motivo_cancelacion = $request->input('motivo');
        $producto->solicitado_por = Auth::id();
        $producto->fecha_solicitud_cancelacion = now();
        $producto->save();

        return redirect()->route('mesa.show', $mesa_id)->with('success', 'Solicitud de cancelación enviada. Esperando aprobación del administrador.');
    }

    /**
     * Aprobar cancelación (admin)
     */
    public function aprobarCancelacion($id)
    {
        date_default_timezone_set('America/Bogota');
        
        $producto = ElementTable::findOrFail($id);
        $mesa_id = $producto->table_id;
        
        $producto->estado = ElementTable::ESTADO_CANCELADO;
        $producto->aprobado_por = Auth::id();
        $producto->fecha_cancelacion = now();
        $producto->save();

        return redirect()->back()->with('success', 'Cancelación aprobada correctamente.');
    }

    /**
     * Rechazar cancelación (admin)
     */
    public function rechazarCancelacion($id)
    {
        $producto = ElementTable::findOrFail($id);
        
        // Volver al estado anterior (listo o pendiente)
        $producto->estado = ElementTable::ESTADO_PENDIENTE;
        $producto->motivo_cancelacion = null;
        $producto->solicitado_por = null;
        $producto->fecha_solicitud_cancelacion = null;
        $producto->save();

        return redirect()->back()->with('success', 'Solicitud de cancelación rechazada.');
    }

    /**
     * Ver cancelaciones pendientes (admin)
     */
    public function cancelacionesPendientes()
    {
        $cancelaciones = ElementTable::with(['producto', 'mesa', 'solicitadoPor'])
            ->where('estado', ElementTable::ESTADO_CANCELACION_SOLICITADA)
            ->orderBy('fecha_solicitud_cancelacion', 'asc')
            ->get();

        return view('cancelaciones.pendientes', compact('cancelaciones'));
    }
}