<?php

namespace Database\Factories;

use App\Models\MenuSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuSectionFactory extends Factory
{
    protected $model = MenuSection::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Pizzas','Lanches','Bebidas','Porções','Doces']);
        return [
            'name'       => $name,
            'sort_order' => $this->faker->numberBetween(1,9)*10,
        ];
    }
}
