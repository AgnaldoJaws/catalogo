<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitiesValeDoRibeiraSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Apiaí',
            'Barra do Chapéu',
            'Barra do Turvo',
            'Cajati',
            'Cananéia',
            'Eldorado',
            'Iguape',
            'Ilha Comprida',
            'Iporanga',
            'Itaóca',
            'Itapirapuã Paulista',
            'Itariri',
            'Jacupiranga',
            'Juquiá',
            'Juquitiba',
            'Miracatu',
            'Pariquera-Açu',
            'Pedro de Toledo',
            'Registro',
            'Ribeira',
            'São Lourenço da Serra',
            'Sete Barras'
        ];

        foreach ($cities as $name) {
            City::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'state_code' => 'SP',
                    // lat/lng são opcionais; podem ser preenchidos depois
                    'lat' => null,
                    'lng' => null,
                ]
            );
        }
    }
}
