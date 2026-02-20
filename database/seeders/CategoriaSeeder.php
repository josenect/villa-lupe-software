<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            ['nombre' => 'Restaurante Almuerzos',  'slug' => 'restaurante-almuerzos',  'es_cocina' => true],
            ['nombre' => 'Restaurante Bebidas',     'slug' => 'restaurante-bebida',     'es_cocina' => true],
            ['nombre' => 'Restaurante Adicional',   'slug' => 'restaurante-adicional',  'es_cocina' => true],
            ['nombre' => 'Caseta',                  'slug' => 'caseta',                 'es_cocina' => false],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['activo' => true])
            );
        }
    }
}
