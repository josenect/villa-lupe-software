<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $slug = Categoria::generarSlug($request->input('nombre'));

        if (Categoria::where('slug', $slug)->exists()) {
            return redirect()->route('admin.categorias.index')
                ->with('error', 'Ya existe una categoría con un nombre similar.');
        }

        Categoria::create([
            'nombre'    => $request->input('nombre'),
            'slug'      => $slug,
            'es_cocina' => $request->boolean('es_cocina'),
            'activo'    => true,
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->nombre    = $request->input('nombre');
        $categoria->es_cocina = $request->boolean('es_cocina');
        $categoria->save();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function toggle($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->activo = !$categoria->activo;
        $categoria->save();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Estado de la categoría actualizado.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        $enUso = Producto::where('category', $categoria->slug)->exists();
        if ($enUso) {
            return redirect()->route('admin.categorias.index')
                ->with('error', 'No se puede eliminar: hay productos usando esta categoría.');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada.');
    }
}
