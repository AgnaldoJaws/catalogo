<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Pizzaria','Burger','Temakeria','Massas','Doceria','Cantina'])
            .' '.$this->faker->randomElement(['da Praça','do Vale','Gourmet','Express','& Cia']);
        return [
            'user_id'     => 1,
            'name'        => $name,
            'slug'        => Str::slug($name.' '.Str::random(4)),
            'logo_url'    => $this->faker->imageUrl(200,200,'food',true),
            'about'       => $this->faker->sentence(10),
            'avg_rating'  => $this->faker->randomFloat(1,3.5,4.9),
            'items_count' => 0,
            'status'      => 1,
        ];
    }
}
