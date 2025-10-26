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
            ['Pizzas',              '🍕'],
            ['Lanches & Burgers',   '🍔'],
            ['Marmitas & Pratos',   '🍛'],
            ['Comida Caseira',      '🍲'],
            ['Porções & Petiscos',  '🍟'],
            ['Sushi & Temaki',      '🍣'],
            ['Açaí & Sorvetes',     '🍨'],
            ['Doces & Sobremesas',  '🍰'],
            ['Padarias & Cafés',    '☕'],
            ['Bebidas & Sucos',     '🥤'],
            ['Cervejas & Drinks',   '🍺'],
            ['Churrasco & Grelhados','🥩'],
            ['Massas & Lasanhas',   '🍝'],
            ['Saudável & Fitness',  '🥗'],
            ['Delivery em Geral',   '🚚'],
        ];

        foreach ($cats as [$name, $emoji]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon_emoji' => $emoji]
            );
        }
    }
}
