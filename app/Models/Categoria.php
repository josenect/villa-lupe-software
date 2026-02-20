<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'slug', 'es_cocina', 'activo'];

    protected $casts = [
        'es_cocina' => 'boolean',
        'activo'    => 'boolean',
    ];

    /**
     * Retorna los slugs de las categorías marcadas como cocina.
     * Usado en CocinaController y MeseroPedidosController.
     */
    public static function slugsCocina(): array
    {
        return static::where('es_cocina', true)
            ->where('activo', true)
            ->pluck('slug')
            ->toArray();
    }

    /**
     * Genera un slug a partir de un nombre.
     */
    public static function generarSlug(string $nombre): string
    {
        return Str::slug($nombre);
    }
}
