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
            ['Pizza','🍕'], ['Lanches','🍔'], ['Marmita','🥗'], ['Japonesa','🍣'],
            ['Massas','🍝'], ['Doces','🍰'], ['Mexicana','🌮'], ['Saudável','🥑'],
            ['Açaí','🫐'], ['Churrasco','🥩'], ['Árabe','🥙'], ['Bebidas','🥤']
        ];

        foreach ($cats as [$name,$emoji]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon_emoji' => $emoji]
            );
        }
    }
}
