<?php

namespace Database\Factories;

use App\Models\BusinessLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessLocationFactory extends Factory
{
    protected $model = BusinessLocation::class;

    public function definition(): array
    {
        // retângulo amplo do Vale do Ribeira
        $lat = $this->faker->randomFloat(6, -25.5, -23.9);
        $lng = $this->faker->randomFloat(6, -49.2, -46.8);

        return [
            'city_id'  => 1,
            'name'     => $this->faker->optional()->streetName(),
            'address'  => $this->faker->streetAddress(),
            'lat'      => $lat,
            'lng'      => $lng,
            'whatsapp' => '55'.$this->faker->numerify('13########'),
            'phone'    => '55'.$this->faker->numerify('11########'),
            'status'   => 1,
        ];
    }
}
