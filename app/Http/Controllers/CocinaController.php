<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElementTable;

class CocinaController extends Controller
{
    // Tiempo de actualización en milisegundos (15 segundos)
    const REFRESH_TIME = 15000;
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
        $pedidos = $this->getPedidosPendientes();
        $pedidosListos = $this->getPedidosListos();
        $pedidosEntregados = $this->getPedidosEntregadosHoy();
        $refreshTime = self::REFRESH_TIME;

        return view('cocina.index', compact('pedidos', 'pedidosListos', 'pedidosEntregados', 'refreshTime'));
    }

    /**
     * Obtener pedidos vía AJAX (sin recargar página)
     */
    public function getPedidosAjax()
    {
        $pedidos = $this->getPedidosPendientes();
        $pedidosListos = $this->getPedidosListos();
        $pedidosEntregados = $this->getPedidosEntregadosHoy();

        return response()->json([
            'pedidos' => $pedidos->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'amount' => $pedido->amount,
                    'estado' => $pedido->estado,
                    'observacion' => $pedido->observacion,
                    'record' => date('H:i', strtotime($pedido->record)),
                    'producto_nombre' => $pedido->producto->name,
                    'producto_precio' => number_format($pedido->producto->price, 0, ',', '.'),
                    'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                    'mesero_nombre' => $pedido->usuario->name ?? 'N/A',
                ];
            }),
            'pedidosListos' => $pedidosListos->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'amount' => $pedido->amount,
                    'producto_nombre' => $pedido->producto->name,
                    'producto_precio' => number_format($pedido->producto->price, 0, ',', '.'),
                    'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                    'mesero_nombre' => $pedido->usuario->name ?? 'N/A',
                ];
            }),
            'pedidosEntregados' => $pedidosEntregados->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'amount' => $pedido->amount,
                    'producto_nombre' => $pedido->producto->name,
                    'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                    'mesero_nombre' => $pedido->usuario->name ?? 'N/A',
                    'updated_at' => $pedido->updated_at->format('H:i'),
                    'facturado' => $pedido->status == 0,
                ];
            }),
            'contadores' => [
                'pendientes' => $pedidos->where('estado', 'pendiente')->count(),
                'en_cocina' => $pedidos->where('estado', 'en_cocina')->count(),
                'listos' => $pedidosListos->count(),
                'entregados' => $pedidosEntregados->count(),
            ]
        ]);
    }

    /**
     * Obtener pedidos pendientes y en cocina
     */
    private function getPedidosPendientes()
    {
        return ElementTable::with(['producto', 'mesa', 'usuario'])
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
    }

    /**
     * Obtener pedidos listos
     */
    private function getPedidosListos()
    {
        return ElementTable::with(['producto', 'mesa', 'usuario'])
            ->where('status', 1)
            ->where('estado', ElementTable::ESTADO_LISTO)
            ->whereHas('producto', function ($query) {
                $query->whereIn('category', self::CATEGORIAS_COCINA);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener pedidos entregados hoy (últimas 24 horas)
     */
    private function getPedidosEntregadosHoy()
    {
        $desde = now()->subHours(24);
        
        return ElementTable::with(['producto', 'mesa', 'usuario'])
            ->where('updated_at', '>=', $desde)
            ->where(function($q) {
                // Entregados pendientes de facturar
                $q->where(function($sub) {
                    $sub->where('status', 1)
                        ->where('estado', ElementTable::ESTADO_ENTREGADO);
                })
                // O ya facturados
                ->orWhere('status', 0);
            })
            ->whereHas('producto', function ($query) {
                $query->whereIn('category', self::CATEGORIAS_COCINA);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Marcar un producto como listo
     */
    public function marcarListo(Request $request, $id)
    {
        $pedido = ElementTable::findOrFail($id);
        $pedido->estado = ElementTable::ESTADO_LISTO;
        $pedido->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido marcado como listo.',
                'pedido_id' => $id
            ]);
        }

        return redirect()->route('cocina.index')->with('success', 'Pedido marcado como listo.');
    }

    /**
     * Marcar un producto como en cocina
     */
    public function marcarEnCocina(Request $request, $id)
    {
        $pedido = ElementTable::findOrFail($id);
        $pedido->estado = ElementTable::ESTADO_EN_COCINA;
        $pedido->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido marcado en cocina.',
                'pedido_id' => $id
            ]);
        }

        return redirect()->route('cocina.index')->with('success', 'Pedido marcado en cocina.');
    }
}
