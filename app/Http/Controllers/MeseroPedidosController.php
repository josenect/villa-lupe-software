<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElementTable;
use Illuminate\Support\Facades\Auth;

class MeseroPedidosController extends Controller
{
    // Tiempo de actualización en milisegundos (10 segundos)
    const REFRESH_TIME = 10000;
    
    // Categorías de cocina
    const CATEGORIAS_COCINA = [
        'restaurante-almuerzos',
        'restaurante-bebida',
        'restaurante-adicional',
    ];

    /**
     * Vista principal - Mis pedidos
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Pedidos de COCINA
        $pedidosListosCocina = $this->getPedidosListos($userId, true);
        $pedidosEnProcesoCocina = $this->getPedidosEnProceso($userId, true);
        $pedidosEntregadosCocina = $this->getPedidosEntregadosHoy($userId, true);
        
        // Pedidos de OTROS (caseta, etc)
        $pedidosListosOtros = $this->getPedidosListos($userId, false);
        $pedidosEnProcesoOtros = $this->getPedidosEnProceso($userId, false);
        $pedidosEntregadosOtros = $this->getPedidosEntregadosHoy($userId, false);
        
        $refreshTime = self::REFRESH_TIME;

        return view('mesero.mis-pedidos', compact(
            'pedidosListosCocina', 
            'pedidosEnProcesoCocina',
            'pedidosEntregadosCocina',
            'pedidosListosOtros',
            'pedidosEnProcesoOtros',
            'pedidosEntregadosOtros',
            'refreshTime'
        ));
    }

    /**
     * Obtener pedidos vía AJAX
     */
    public function getPedidosAjax()
    {
        $userId = Auth::id();
        
        // Pedidos de COCINA
        $pedidosListosCocina = $this->getPedidosListos($userId, true);
        $pedidosEnProcesoCocina = $this->getPedidosEnProceso($userId, true);
        $pedidosEntregadosCocina = $this->getPedidosEntregadosHoy($userId, true);
        
        // Pedidos de OTROS
        $pedidosListosOtros = $this->getPedidosListos($userId, false);
        $pedidosEnProcesoOtros = $this->getPedidosEnProceso($userId, false);
        $pedidosEntregadosOtros = $this->getPedidosEntregadosHoy($userId, false);

        return response()->json([
            'cocina' => [
                'listos' => $this->mapPedidosListos($pedidosListosCocina),
                'enProceso' => $this->mapPedidosEnProceso($pedidosEnProcesoCocina),
                'entregados' => $this->mapPedidosEntregados($pedidosEntregadosCocina),
            ],
            'otros' => [
                'listos' => $this->mapPedidosListos($pedidosListosOtros),
                'enProceso' => $this->mapPedidosEnProceso($pedidosEnProcesoOtros),
                'entregados' => $this->mapPedidosEntregados($pedidosEntregadosOtros),
            ],
            'contadores' => [
                'listos_cocina' => $pedidosListosCocina->count(),
                'proceso_cocina' => $pedidosEnProcesoCocina->count(),
                'entregados_cocina' => $pedidosEntregadosCocina->count(),
                'listos_otros' => $pedidosListosOtros->count(),
                'proceso_otros' => $pedidosEnProcesoOtros->count(),
                'entregados_otros' => $pedidosEntregadosOtros->count(),
            ]
        ]);
    }
    
    /**
     * Mapear pedidos entregados para JSON
     */
    private function mapPedidosEntregados($pedidos)
    {
        return $pedidos->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'amount' => $pedido->amount,
                'producto_nombre' => $pedido->producto->name,
                'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                'updated_at' => $pedido->updated_at->format('H:i'),
                'facturado' => $pedido->status == 0,
            ];
        });
    }
    
    /**
     * Mapear pedidos listos para JSON
     */
    private function mapPedidosListos($pedidos)
    {
        return $pedidos->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'amount' => $pedido->amount,
                'producto_nombre' => $pedido->producto->name,
                'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                'observacion' => $pedido->observacion,
                'updated_at' => $pedido->updated_at->format('H:i'),
            ];
        });
    }
    
    /**
     * Mapear pedidos en proceso para JSON
     */
    private function mapPedidosEnProceso($pedidos)
    {
        return $pedidos->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'amount' => $pedido->amount,
                'estado' => $pedido->estado,
                'producto_nombre' => $pedido->producto->name,
                'mesa_nombre' => $pedido->mesa->name ?? 'Mesa',
                'observacion' => $pedido->observacion,
                'record' => date('H:i', strtotime($pedido->record)),
            ];
        });
    }

    /**
     * Marcar pedido como entregado
     */
    public function marcarEntregado(Request $request, $id)
    {
        $pedido = ElementTable::findOrFail($id);
        
        // Verificar que el pedido pertenezca al mesero actual o sea admin
        if ($pedido->user_id !== Auth::id() && !Auth::user()->esAdmin()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }
            return redirect()->back()->with('error', 'No autorizado');
        }

        // Marcar como entregado (el producto sigue en la mesa para facturar)
        $pedido->estado = ElementTable::ESTADO_ENTREGADO;
        $pedido->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido marcado como entregado'
            ]);
        }

        return redirect()->back()->with('success', 'Pedido marcado como entregado');
    }

    /**
     * Obtener pedidos listos para llevar
     * @param bool $esCocina - true para cocina, false para otros
     */
    private function getPedidosListos($userId, $esCocina = true)
    {
        $query = ElementTable::with(['producto', 'mesa'])
            ->where('status', 1)
            ->where('estado', ElementTable::ESTADO_LISTO)
            ->where('user_id', $userId);
            
        if ($esCocina) {
            $query->whereHas('producto', function ($q) {
                $q->whereIn('category', self::CATEGORIAS_COCINA);
            });
        } else {
            $query->whereHas('producto', function ($q) {
                $q->whereNotIn('category', self::CATEGORIAS_COCINA);
            });
        }
        
        return $query->orderBy('updated_at', 'asc')->get();
    }

    /**
     * Obtener pedidos en proceso
     * @param bool $esCocina - true para cocina, false para otros
     */
    private function getPedidosEnProceso($userId, $esCocina = true)
    {
        $query = ElementTable::with(['producto', 'mesa'])
            ->where('status', 1)
            ->where('user_id', $userId)
            ->whereIn('estado', [
                ElementTable::ESTADO_PENDIENTE,
                ElementTable::ESTADO_EN_COCINA
            ]);
            
        if ($esCocina) {
            $query->whereHas('producto', function ($q) {
                $q->whereIn('category', self::CATEGORIAS_COCINA);
            });
        } else {
            $query->whereHas('producto', function ($q) {
                $q->whereNotIn('category', self::CATEGORIAS_COCINA);
            });
        }
        
        return $query->orderBy('record', 'asc')->get();
    }
    
    /**
     * Obtener pedidos entregados de hoy (incluye facturados y pendientes de facturar)
     * @param bool $esCocina - true para cocina, false para otros
     */
    private function getPedidosEntregadosHoy($userId, $esCocina = true)
    {
        // Usar las últimas 24 horas para evitar problemas de zona horaria
        $desde = now()->subHours(24);
        
        $query = ElementTable::with(['producto', 'mesa'])
            ->where('user_id', $userId)
            ->where('updated_at', '>=', $desde)
            ->where(function($q) {
                // Entregados pendientes de facturar
                $q->where(function($sub) {
                    $sub->where('status', 1)
                        ->where('estado', ElementTable::ESTADO_ENTREGADO);
                })
                // O ya facturados
                ->orWhere('status', 0);
            });
            
        if ($esCocina) {
            $query->whereHas('producto', function ($q) {
                $q->whereIn('category', self::CATEGORIAS_COCINA);
            });
        } else {
            $query->whereHas('producto', function ($q) {
                $q->whereNotIn('category', self::CATEGORIAS_COCINA);
            });
        }
        
        return $query->orderBy('updated_at', 'desc')->get();
    }
}
