<?php

namespace Database\Seeders;

use App\Models\{
    Business, BusinessLocation, BusinessHour, Category, City,
    MenuSection, MenuItem, MenuItemOption, User
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BusinessesBigSeedSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        $cities = City::pluck('id','slug');            // ['registro'=>1, ...]
        $categories = Category::all();
        $owners = User::where('role','owner')->pluck('id')->all();

        if ($cities->isEmpty() || $categories->isEmpty() || empty($owners)) {
            $this->command->warn("Execute CitiesValeDoRibeiraSeeder, CategoriesSeeder e DemoOwnersSeeder antes.");
            return;
        }

        // Limites aproximados do Vale do Ribeira (para gerar lat/lng realistas)
        // (retângulo amplo: ajuste se quiser)
        $latMin = -25.5; $latMax = -23.9;
        $lngMin = -49.2; $lngMax = -46.8;

        // Por cidade: 12–18 negócios
        foreach ($cities as $citySlug => $cityId) {
            $bizCount = $faker->numberBetween(12, 18);

            for ($b=0; $b<$bizCount; $b++) {
                $bizName = $this->randomBusinessName($faker);
                $business = Business::create([
                    'user_id'     => $faker->randomElement($owners),
                    'name'        => $bizName,
                    'slug'        => Str::slug($bizName.' '.$citySlug.' '.Str::random(4)),
                    'logo_url'    => $faker->imageUrl(200, 200, 'food', true),
                    'about'       => $faker->sentence(12),
                    'avg_rating'  => $faker->randomFloat(1, 3.5, 4.9),
                    'items_count' => 0,
                    'status'      => 1,
                ]);

                // Categorias (1–3)
                $attach = $categories->random($faker->numberBetween(1,3))->pluck('id')->all();
                $business->categories()->sync($attach);

                // Filiais (1–2) na mesma cidade
                $locCount = $faker->numberBetween(1,2);
                $businessItemsCounter = 0;

                for ($l=0; $l<$locCount; $l++) {
                    $lat = $faker->randomFloat(6, $latMin, $latMax);
                    $lng = $faker->randomFloat(6, $lngMin, $lngMax);

                    $location = BusinessLocation::create([
                        'business_id' => $business->id,
                        'city_id'     => $cityId,
                        'name'        => $faker->randomElement([null, $faker->streetName()]),
                        'address'     => $faker->streetAddress(),
                        'lat'         => $lat,
                        'lng'         => $lng,
                        'whatsapp'    => '55'.$faker->numerify('13########'), // DDD 13/15/11 etc — aqui usei 13 como exemplo
                        'phone'       => '55'.$faker->numerify('11########'),
                        'status'      => 1,
                    ]);

                    // Horários (7 dias)
                    for ($w=0; $w<7; $w++) {
                        // 20% chance de overnight (ex.: 18:00–02:00)
                        $overnight = $faker->boolean(20);
                        if ($overnight) {
                            $open = '18:00:00'; $close = '02:00:00';
                        } else {
                            // Faixa padrão almoço/janta (11–22)
                            $openHour  = $faker->numberBetween(10, 12);
                            $closeHour = $faker->numberBetween(21, 23);
                            $open  = sprintf('%02d:00:00', $openHour);
                            $close = sprintf('%02d:00:00', $closeHour);
                        }

                        BusinessHour::create([
                            'business_loc_id' => $location->id,
                            'weekday'         => $w,
                            'open_time'       => $open,
                            'close_time'      => $close,
                            'overnight'       => $overnight,
                        ]);
                    }

                    // Cardápio (3–5 seções, 6–12 itens/ seção)
                    $secCount = $faker->numberBetween(3,5);
                    for ($s=1; $s<=$secCount; $s++) {
                        $secName = $this->randomSectionName($faker);
                        $section = MenuSection::create([
                            'business_id'     => $business->id,
                            'business_loc_id' => null, // herdado da marca; mude para $location->id se quiser por filial
                            'name'            => $secName,
                            'sort_order'      => $s * 10,
                        ]);

                        $itemCount = $faker->numberBetween(6,12);
                        for ($i=1; $i<=$itemCount; $i++) {
                            $itemName = $this->randomItemName($secName, $faker);
                            $priceCents = $faker->numberBetween(1800, 7990);
                            $item = MenuItem::create([
                                'business_id'       => $business->id,
                                'section_id'        => $section->id,
                                'business_loc_id'   => null, // idem acima
                                'name'              => $itemName,
                                'description'       => $faker->sentence(10),
                                'price_cents'       => $priceCents,
                                'prep_time_minutes' => $faker->numberBetween(10, 40),
                                'tags'              => $faker->randomElements(['veg','vegan','gluten-free','low-carb','spicy'], $faker->numberBetween(0,2)),
                                'is_available'      => $faker->boolean(95),
                                'image_url'         => $faker->boolean(60) ? $faker->imageUrl(600, 400, 'food', true) : null,
                                'sort_order'        => $i * 10,
                            ]);

                            // 0–3 opções por item
                            $optCount = $faker->numberBetween(0,3);
                            for ($o=0; $o<$optCount; $o++) {
                                MenuItemOption::create([
                                    'item_id'            => $item->id,
                                    'group_name'         => $faker->randomElement(['Tamanho','Adicionais']),
                                    'name'               => $faker->randomElement(['Pequena','Média','Grande','Bacon','Cheddar','Catupiry']),
                                    'price_delta_cents'  => $faker->randomElement([0, 400, 600, 900]),
                                    'max_select'         => null,
                                ]);
                            }

                            $businessItemsCounter++;
                        }
                    }
                }

                // Atualiza contador de itens na marca (ajuda no "ordenar por mais itens")
                $business->update(['items_count' => $businessItemsCounter]);
            }
        }

        $this->command->info('Seed de grande volume concluído (Vale do Ribeira).');
    }

    private function randomBusinessName($faker): string
    {
        $prefix = $faker->randomElement(['Pizzaria','Burger','Temakeria','Massas','Cantina','Doceria','Sabor','Cantinho','Varanda','Delícias','Casa']);
        $suffix = $faker->randomElement(['da Praça','do Vale','do Chef','& Cia','do Sabor','Express','Gourmet','Brasil']);
        return "{$prefix} {$suffix}";
    }

    private function randomSectionName($faker): string
    {
        return $faker->randomElement(['Pizzas','Lanches','Combos','Bebidas','Porções','Doces','Promoções']);
    }

    private function randomItemName(string $section, $faker): string
    {
        $map = [
            'Pizzas'  => ['Calabresa','Marguerita','Frango c/ Catupiry','Portuguesa','4 Queijos','Mussarela'],
            'Lanches' => ['Burger Clássico','Cheeseburger','X-Bacon','Smash Duplo','Chicken Burger'],
            'Bebidas' => ['Refrigerante Lata','Suco Natural','Água com Gás','Cerveja Long Neck'],
            'Porções' => ['Batata Frita','Onion Rings','Nuggets','Polenta Frita'],
            'Doces'   => ['Pudim','Mousse de Maracujá','Brigadeiro','Brownie'],
            'Combos'  => ['Combo Burger + Refri','Combo Pizza + Refri','Combo Família'],
            'Promoções'=> ['Promo do Dia','Festival da Pizza','Desconto da Semana'],
        ];
        $base = $map[$section] ?? [$faker->word()];
        return $faker->randomElement($base);
    }
}
