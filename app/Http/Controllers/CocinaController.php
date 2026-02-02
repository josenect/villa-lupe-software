<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElementTable;

class CocinaController extends Controller
{
    // Categorías que se muestran en cocina
    const CATEGORIAS_COCINA = [
        'restaurante-almuerzos',
        'restaurante-bebida',
        'restaurante-adicional',
    ];

    /**
     * Vista principal de cocina - pedidos activos
     */
    public function index()
    {
        $pedidos = ElementTable::with(['producto', 'mesa'])
            ->where('status', 1)
            ->whereIn('estado', [
                ElementTable::ESTADO_PENDIENTE,
                ElementTable::ESTADO_EN_COCINA
            ])
            ->whereHas('producto', function ($query) {
                $query->whereIn('category', self::CATEGORIAS_COCINA);
            })
            ->orderBy('record', 'asc')
            ->get();

        $pedidosListos = ElementTable::with(['producto', 'mesa'])
            ->where('status', 1)
            ->where('estado', ElementTable::ESTADO_LISTO)
            ->whereHas('producto', function ($query) {
                $query->whereIn('category', self::CATEGORIAS_COCINA);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('cocina.index', compact('pedidos', 'pedidosListos'));
    }

    /**
     * Marcar un producto como listo
     */
    public function marcarListo($id)
    {
        $pedido = ElementTable::findOrFail($id);
        $pedido->estado = ElementTable::ESTADO_LISTO;
        $pedido->save();

        return redirect()->route('cocina.index')->with('success', 'Pedido marcado como listo.');
    }

    /**
     * Marcar un producto como en cocina
     */
    public function marcarEnCocina($id)
    {
        $pedido = ElementTable::findOrFail($id);
        $pedido->estado = ElementTable::ESTADO_EN_COCINA;
        $pedido->save();

        return redirect()->route('cocina.index')->with('success', 'Pedido marcado en cocina.');
    }
}
