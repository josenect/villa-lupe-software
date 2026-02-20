<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'menu_cocina_visible'      => Setting::get('menu_cocina_visible', '1'),
            'menu_mis_pedidos_visible' => Setting::get('menu_mis_pedidos_visible', '1'),
            'restaurante_nombre'       => Setting::get('restaurante_nombre', 'Villa Lupe'),
            'restaurante_propiedad'    => Setting::get('restaurante_propiedad', 'Casa de Campo'),
            'restaurante_direccion'    => Setting::get('restaurante_direccion', ''),
            'restaurante_logo'         => Setting::get('restaurante_logo', ''),
            'propina_habilitada'       => Setting::get('propina_habilitada', '1'),
            'propina_porcentaje'       => Setting::get('propina_porcentaje', (string) env('PROPINA', 10)),
        ];

        return view('admin.configuracion', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'restaurante_nombre'    => 'required|string|max:100',
            'restaurante_propiedad' => 'nullable|string|max:100',
            'restaurante_direccion' => 'nullable|string|max:200',
            'restaurante_logo'      => 'nullable|image|max:2048',
            'propina_porcentaje'    => 'nullable|integer|min:0|max:100',
        ]);

        Setting::set('menu_cocina_visible',      $request->has('menu_cocina_visible') ? '1' : '0');
        Setting::set('menu_mis_pedidos_visible',  $request->has('menu_mis_pedidos_visible') ? '1' : '0');
        Setting::set('restaurante_nombre',        $request->input('restaurante_nombre'));
        Setting::set('restaurante_propiedad',     $request->input('restaurante_propiedad', ''));
        Setting::set('restaurante_direccion',     $request->input('restaurante_direccion', ''));
        Setting::set('propina_habilitada',        $request->has('propina_habilitada') ? '1' : '0');
        Setting::set('propina_porcentaje',        (string) (int) $request->input('propina_porcentaje', 0));

        if ($request->hasFile('restaurante_logo')) {
            $path = $request->file('restaurante_logo')->store('logos', 'public');
            Setting::set('restaurante_logo', $path);
        }

        return redirect()->route('admin.configuracion')->with('success', 'Configuración guardada correctamente.');
    }
}
