<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Domicilio;
use App\Models\ElementTable;
use Illuminate\Http\Request;

class DomicilioController extends Controller
{
    /**
     * Obtener o crear un slot (virtual table) disponible para un domicilio.
     * Un slot esta disponible si no tiene un domicilio activo vinculado.
     */
    private function obtenerSlot()
    {
        // Buscar un slot libre (sin domicilio activo)
        $slot = Table::domicilioSlots()
            ->whereDoesntHave('domicilio')
            ->first();

        if ($slot) {
            return $slot;
        }

        // Crear un nuevo slot
        $numero = Table::domicilioSlots()->count() + 1;
        return Table::create([
            'name'         => 'DOM-' . str_pad($numero, 3, '0', STR_PAD_LEFT),
            'location'     => 'Domicilio',
            'status'       => 1,
            'is_domicilio' => true,
        ]);
    }

    public function index()
    {
        $domicilios = Domicilio::activos()
            ->with('mesa')
            ->get()
            ->map(function ($dom) {
                if (!$dom->mesa) return null;

                $productos = ElementTable::where('table_id', $dom->table_id)
                    ->where('status', 1)
                    ->where('estado', '!=', ElementTable::ESTADO_CANCELADO)
                    ->get();

                $dom->total_productos = $productos->count();
                $dom->subtotal = $productos->sum(function ($p) {
                    return ($p->price * $p->amount) - ($p->dicount * $p->amount);
                });

                return $dom;
            })
            ->filter();

        return view('domicilios.index', compact('domicilios'));
    }

    public function create()
    {
        return view('domicilios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre'    => 'required|string|max:100',
            'cliente_telefono'  => 'required|string|max:20',
            'cliente_direccion' => 'required|string|max:255',
        ]);

        $slot = $this->obtenerSlot();

        // Activar el slot
        $slot->update([
            'status'      => 1,
            'occupied_at' => now(),
        ]);

        // Crear el registro de domicilio
        $domicilio = Domicilio::create([
            'table_id'          => $slot->id,
            'cliente_nombre'    => $request->input('cliente_nombre'),
            'cliente_telefono'  => $request->input('cliente_telefono'),
            'cliente_direccion' => $request->input('cliente_direccion'),
            'estado'            => Domicilio::ESTADO_ACTIVO,
        ]);

        return redirect()->route('mesa.show', $slot->id)->with('success', 'Domicilio creado. Agrega los productos del pedido.');
    }

    public function edit($id)
    {
        $domicilio = Domicilio::activos()->findOrFail($id);

        return view('domicilios.editar', compact('domicilio'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'cliente_nombre'    => 'required|string|max:100',
            'cliente_telefono'  => 'required|string|max:20',
            'cliente_direccion' => 'required|string|max:255',
        ]);

        $domicilio = Domicilio::activos()->findOrFail($id);
        $domicilio->update([
            'cliente_nombre'    => $request->input('cliente_nombre'),
            'cliente_telefono'  => $request->input('cliente_telefono'),
            'cliente_direccion' => $request->input('cliente_direccion'),
        ]);

        return redirect()->route('mesa.show', $domicilio->table_id)->with('success', 'Datos del domicilio actualizados.');
    }

    public function cancelar($id)
    {
        $domicilio = Domicilio::activos()->findOrFail($id);

        // Cancelar productos activos
        ElementTable::where('table_id', $domicilio->table_id)
            ->where('status', 1)
            ->update([
                'status' => 0,
                'estado' => ElementTable::ESTADO_CANCELADO,
            ]);

        // Marcar domicilio como cancelado y liberar slot
        $domicilio->update(['estado' => Domicilio::ESTADO_CANCELADO]);

        if ($domicilio->mesa) {
            $domicilio->mesa->update([
                'status'      => 0,
                'occupied_at' => null,
            ]);
        }

        return redirect()->route('domicilios.index')->with('success', 'Domicilio cancelado correctamente.');
    }

    public function historial(Request $request)
    {
        $desde = $request->get('desde', date('Y-m-d'));
        $hasta = $request->get('hasta', $desde);

        $facturados = Domicilio::facturados()
            ->whereDate('updated_at', '>=', $desde)
            ->whereDate('updated_at', '<=', $hasta)
            ->with(['mesa.facturas' => function ($q) use ($desde, $hasta) {
                $q->whereDate('fecha_hora_factura', '>=', $desde)
                  ->whereDate('fecha_hora_factura', '<=', $hasta)
                  ->orderBy('fecha_hora_factura', 'desc');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        $cancelados = Domicilio::cancelados()
            ->whereDate('updated_at', '>=', $desde)
            ->whereDate('updated_at', '<=', $hasta)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('domicilios.historial', compact('facturados', 'cancelados', 'desde', 'hasta'));
    }
}
