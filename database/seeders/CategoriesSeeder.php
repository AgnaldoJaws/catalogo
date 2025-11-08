<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['Hortaliças & Verduras', '🥬'],
            ['Frutas Frescas', '🍎'],
            ['Legumes & Raízes', '🥕'],
            ['Grãos & Cereais', '🌾'],
            ['Produtos Orgânicos', '🍃'],
            ['Mel & Derivados', '🍯'],
            ['Ovos & Laticínios', '🥚'],
            ['Cafés & Chás Artesanais', '☕'],
            ['Plantas & Mudas', '🪴'],
            ['Temperos & Especiarias', '🌿'],
            ['Pães & Bolos Caseiros', '🍞'],
            ['Sucos & Polpas Naturais', '🍹'],
            ['Artesanato Rural', '🧺'],
            ['Cestas & Kits Sustentáveis', '🧃'],
            ['Produtos da Roça', '🚜'],
        ];

        foreach ($cats as [$name, $emoji]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon_emoji' => $emoji]
            );
        }
    }
}
