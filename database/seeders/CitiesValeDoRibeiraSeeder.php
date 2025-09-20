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
            'Registro','Iguape','Cananéia','Ilha Comprida','Pariquera-Açu','Jacupiranga',
            'Cajati','Eldorado','Sete Barras','Miracatu','Juquiá','Pedro de Toledo',
            'Itariri','Barra do Turvo'
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
