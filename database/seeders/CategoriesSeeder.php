<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Catálogo amplo para delivery de comida e bebida
        $cats = [
            // 🍕 Pizzarias e Massas
            ['Pizzas',                    '🍕'],
            ['Massas & Molhos',           '🍝'],
            ['Lasanhas & Assados',        '🥘'],

            // 🍔 Lanches e Hamburguerias
            ['Hambúrgueres Artesanais',   '🍔'],
            ['Lanches & Sanduíches',      '🥪'],
            ['Hot Dogs',                  '🌭'],
            ['Porções & Acompanhamentos', '🍟'],

            // 🍣 Cozinha Oriental
            ['Sushi & Sashimi',           '🍣'],
            ['Temakis',                   '🥢'],
            ['Combos Orientais',          '🍱'],

            // 🍛 Marmitas, Pratos e Comida Caseira
            ['Pratos do Dia',             '🍛'],
            ['Marmitas',                  '🥗'],
            ['Comida Caseira',            '🍲'],
            ['Churrasco & Grelhados',     '🥩'],

            // ☕ Cafés, Doces e Padarias
            ['Cafés & Bebidas Quentes',   '☕'],
            ['Padaria & Confeitaria',     '🥐'],
            ['Doces & Sobremesas',        '🍰'],
            ['Açaí & Sorvetes',           '🍨'],

            // 🥤 Bebidas
            ['Refrigerantes & Sucos',     '🥤'],
            ['Cervejas & Drinks',         '🍺'],
            ['Águas & Naturais',          '💧'],

            // 🍴 Categorias de Estabelecimentos
            ['Pizzarias',                 '🏠'],
            ['Hamburguerias',             '🍔'],
            ['Lanchonetes',               '🥪'],
            ['Sushi Bars',                '🍣'],
            ['Marmitarias',               '🍱'],
            ['Churrascarias',             '🔥'],
            ['Cafeterias',                '☕'],
            ['Padarias',                  '🥖'],
            ['Docerias',                  '🧁'],
            ['Açaíterias',                '🍇'],
            ['Restaurantes',              '🍽️'],
            ['Delivery em Geral',         '🚚'],
        ];

        foreach ($cats as [$name, $emoji]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon_emoji' => $emoji]
            );
        }
    }
}
