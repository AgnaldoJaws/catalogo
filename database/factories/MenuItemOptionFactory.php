<?php

namespace Database\Factories;

use App\Models\MenuItemOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemOptionFactory extends Factory
{
    protected $model = MenuItemOption::class;

    public function definition(): array
    {
        return [
            'group_name'        => $this->faker->randomElement(['Tamanho','Adicionais']),
            'name'              => $this->faker->randomElement(['Pequena','Média','Grande','Bacon','Cheddar','Catupiry']),
            'price_delta_cents' => $this->faker->randomElement([0, 400, 600, 900]),
            'max_select'        => null,
        ];
    }
}
