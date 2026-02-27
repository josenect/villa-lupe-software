<?php

namespace App\Http\Controllers;

use App\Models\ElementTable;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Endpoint ultraligero para polling inteligente.
     * Devuelve hashes + IDs para que el cliente detecte cambios.
     * Respuesta típica: ~80 bytes, <50ms.
     */
    public function check()
    {
        $user    = Auth::user();
        $userId  = $user->id;
        $userRol = $user->rol;
        $result  = [];

        // Mesero/Admin: IDs de pedidos listos
        if (in_array($userRol, ['mesero', 'admin'])) {
            $ids = ElementTable::where('status', 1)
                ->where('estado', ElementTable::ESTADO_LISTO)
                ->where('user_id', $userId)
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $result['m'] = $ids;
            $result['mh'] = crc32(implode(',', $ids));
        }

        // Cocina/Admin: IDs de pedidos pendientes/en cocina
        if (in_array($userRol, ['cocina', 'admin'])) {
            $ids = ElementTable::where('status', 1)
                ->whereIn('estado', [
                    ElementTable::ESTADO_PENDIENTE,
                    ElementTable::ESTADO_EN_COCINA,
                ])
                ->whereHas('producto', function ($q) {
                    $q->whereIn('category', Categoria::slugsCocina());
                })
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $result['c'] = $ids;
            $result['ch'] = crc32(implode(',', $ids));
        }

        return response()->json($result);
    }
}
