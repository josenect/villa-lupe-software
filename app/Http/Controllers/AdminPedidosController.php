<?php

namespace App\Http\Controllers;

use App\Models\ElementTable;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPedidosController extends Controller
{
    /**
     * Vista principal - Pedidos por mesero
     */
    public function index(Request $request)
    {
        $meseros = User::whereIn('rol', ['mesero', 'admin'])->orderBy('name')->get();
        $meseroSeleccionado = $request->input('mesero_id');
        
        $pedidosPendientes = collect();
        $pedidosEntregados = collect();
        $meseroNombre = 'Todos';
        
        if ($meseroSeleccionado) {
            $mesero = User::find($meseroSeleccionado);
            $meseroNombre = $mesero ? $mesero->name : 'Todos';
            $pedidosPendientes = $this->getPedidosPendientes($meseroSeleccionado);
            $pedidosEntregados = $this->getPedidosEntregados($meseroSeleccionado);
        } else {
            // Mostrar de todos los meseros
            $pedidosPendientes = $this->getPedidosPendientes(null);
            $pedidosEntregados = $this->getPedidosEntregados(null);
        }

        return view('admin.pedidos-meseros', compact(
            'meseros',
            'meseroSeleccionado',
            'meseroNombre',
            'pedidosPendientes',
            'pedidosEntregados'
        ));
    }

    /**
     * Obtener pedidos pendientes de entregar
     */
    private function getPedidosPendientes($userId = null)
    {
        $query = ElementTable::with(['producto', 'mesa', 'usuario'])
            ->where('status', 1)
            ->whereIn('estado', [
                ElementTable::ESTADO_PENDIENTE,
                ElementTable::ESTADO_EN_COCINA,
                ElementTable::ESTADO_LISTO
            ]);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->orderBy('record', 'asc')->get();
    }

    /**
     * Obtener pedidos entregados (últimas 24 horas)
     */
    private function getPedidosEntregados($userId = null)
    {
        $desde = now()->subHours(24);
        
        $query = ElementTable::with(['producto', 'mesa', 'usuario'])
            ->where('updated_at', '>=', $desde)
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->where('status', 1)
                        ->where('estado', ElementTable::ESTADO_ENTREGADO);
                })
                ->orWhere('status', 0);
            });
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Resumen de pedidos por mesero (AJAX)
     */
    public function resumen()
    {
        $meseros = User::whereIn('rol', ['mesero', 'admin'])->orderBy('name')->get();
        
        $resumen = $meseros->map(function($mesero) {
            $pendientes = ElementTable::where('user_id', $mesero->id)
                ->where('status', 1)
                ->whereIn('estado', [
                    ElementTable::ESTADO_PENDIENTE,
                    ElementTable::ESTADO_EN_COCINA,
                    ElementTable::ESTADO_LISTO
                ])
                ->count();
            
            $entregados = ElementTable::where('user_id', $mesero->id)
                ->where('updated_at', '>=', now()->subHours(24))
                ->where(function($q) {
                    $q->where(function($sub) {
                        $sub->where('status', 1)
                            ->where('estado', ElementTable::ESTADO_ENTREGADO);
                    })
                    ->orWhere('status', 0);
                })
                ->count();
            
            return [
                'id' => $mesero->id,
                'nombre' => $mesero->name,
                'pendientes' => $pendientes,
                'entregados' => $entregados,
            ];
        });

        return response()->json($resumen);
    }
}
