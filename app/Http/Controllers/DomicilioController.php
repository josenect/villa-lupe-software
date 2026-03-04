<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\ElementTable;
use Illuminate\Http\Request;

class DomicilioController extends Controller
{
    public function index()
    {
        $domicilios = Table::domicilios()
            ->where('status', 1)
            ->get()
            ->map(function ($dom) {
                $productos = ElementTable::where('table_id', $dom->id)
                    ->where('status', 1)
                    ->whereNotIn('estado', [ElementTable::ESTADO_CANCELADO])
                    ->get();

                $dom->total_productos = $productos->count();
                $dom->subtotal = $productos->sum(function ($p) {
                    return ($p->price * $p->amount) - ($p->dicount * $p->amount);
                });

                return $dom;
            });

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

        $ultimo = Table::domicilios()->max('id');
        $numero = str_pad(($ultimo ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        $domicilio = Table::create([
            'name'              => 'DOM-' . $numero,
            'location'          => 'Domicilio',
            'status'            => 1,
            'is_domicilio'      => true,
            'cliente_nombre'    => $request->input('cliente_nombre'),
            'cliente_telefono'  => $request->input('cliente_telefono'),
            'cliente_direccion' => $request->input('cliente_direccion'),
            'occupied_at'       => now(),
        ]);

        return redirect()->route('mesa.show', $domicilio->id)->with('success', 'Domicilio creado. Agrega los productos del pedido.');
    }

    public function edit($id)
    {
        $domicilio = Table::domicilios()->findOrFail($id);

        return view('domicilios.editar', compact('domicilio'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'cliente_nombre'    => 'required|string|max:100',
            'cliente_telefono'  => 'required|string|max:20',
            'cliente_direccion' => 'required|string|max:255',
        ]);

        $domicilio = Table::domicilios()->findOrFail($id);
        $domicilio->update([
            'cliente_nombre'    => $request->input('cliente_nombre'),
            'cliente_telefono'  => $request->input('cliente_telefono'),
            'cliente_direccion' => $request->input('cliente_direccion'),
        ]);

        return redirect()->route('mesa.show', $domicilio->id)->with('success', 'Datos del domicilio actualizados.');
    }
}
