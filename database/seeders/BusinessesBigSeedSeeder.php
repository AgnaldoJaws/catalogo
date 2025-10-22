<?php

namespace Database\Seeders;

use App\Models\{
    Business, BusinessLocation, BusinessHour, Category, City,
    MenuSection, MenuItem, MenuItemOption, User
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BusinessesBigSeedSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        $cities     = City::pluck('id','slug');            // ['registro'=>1, ...]
        $categories = Category::all();
        $owners     = User::where('role','owner')->pluck('id')->all();

        if ($cities->isEmpty() || $categories->isEmpty() || empty($owners)) {
            $this->command->warn("Execute CitiesValeDoRibeiraSeeder, CategoriesSeeder e DemoOwnersSeeder antes.");
            return;
        }

        // Retângulo amplo Vale do Ribeira (ajuste se quiser)
        $latMin = -25.5; $latMax = -23.9;
        $lngMin = -49.2; $lngMax = -46.8;

        foreach ($cities as $citySlug => $cityId) {
            $bizCount = $faker->numberBetween(10, 16);

            for ($b=0; $b<$bizCount; $b++) {
                $bizName = $this->randomBusinessName($faker);

                $business = Business::create([
                    'user_id'     => $faker->randomElement($owners),
                    'name'        => $bizName,
                    'slug'        => Str::slug($bizName.' '.$citySlug.' '.Str::random(4)),
                    'logo_url'    => $faker->imageUrl(200, 200, 'food', true),
                    'about'       => $faker->randomElement([
                        'Delivery rápido com sabor de verdade.',
                        'Ingredientes frescos e preparo artesanal.',
                        'Qualidade, preço justo e entrega eficiente.',
                        'Seu pedido favorito com aquele toque caseiro.',
                    ]),
                    'avg_rating'  => $faker->randomFloat(1, 3.7, 4.9),
                    'items_count' => 0,
                    'status'      => 1,
                ]);

                $userEmpresa = \App\Models\User::updateOrCreate(
                    ['email' => 'empresa+' . $business->slug . '@catalogo.local'],
                    [
                        'name'              => 'Empresa ' . $business->name,
                        'password'          => \Illuminate\Support\Facades\Hash::make('123'),
                        'role'              => 'empresa',
                        'phone'             => '119' . str_pad((string)rand(10000000,99999999), 8, '0', STR_PAD_LEFT),
                        'email_verified_at' => now(),
                    ]
                );

                $business->user_id = $userEmpresa->id;
                $business->save();

                // === Categorias coerentes ===
                $attach = $this->pickCategoriesForBusiness($categories, $bizName, $faker);
                $business->categories()->sync($attach);

                // Filiais (1–2)
                $locCount = $faker->numberBetween(1,2);
                $businessItemsCounter = 0;

                for ($l=0; $l<$locCount; $l++) {
                    $lat = $faker->randomFloat(6, $latMin, $latMax);
                    $lng = $faker->randomFloat(6, min($lngMin,$lngMax), max($lngMin,$lngMax));

                    $location = \App\Models\BusinessLocation::create([
                        'business_id' => $business->id,
                        'city_id'     => $cityId,
                        'name'        => $faker->randomElement([null, $faker->streetName()]),
                        'address'     => $faker->streetAddress(),
                        'lat'         => $lat,
                        'lng'         => $lng,
                        'whatsapp'    => '55'.$faker->numerify('13########'),
                        'phone'       => '55'.$faker->numerify('11########'),
                        'status'      => 1,
                    ]);

                    // Horários (7 dias)
                    for ($w=0; $w<7; $w++) {
                        $overnight = $faker->boolean(15);
                        $openHour  = $faker->numberBetween(10, 18);
                        $closeHour = $overnight ? 23 : $faker->numberBetween(21, 23);
                        $open  = sprintf('%02d:00:00', $openHour);
                        $close = sprintf('%02d:59:00', $closeHour);

                        \App\Models\BusinessHour::create([
                            'business_loc_id' => $location->id,
                            'weekday'         => $w,
                            'open_time'       => $open,
                            'close_time'      => $close,
                            'overnight'       => $overnight,
                        ]);
                    }

                    // Cardápio/Itens
                    $sections = $this->sectionsForBusiness($bizName);
                    $sort = 10;

                    foreach ($sections as $secName) {
                        $section = \App\Models\MenuSection::create([
                            'business_id'     => $business->id,
                            'business_loc_id' => null,
                            'name'            => $secName,
                            'sort_order'      => $sort,
                        ]);
                        $sort += 10;

                        $itemCount = $faker->numberBetween(6, 12);
                        for ($i=1; $i<=$itemCount; $i++) {
                            $itemName    = $this->randomItemName($secName, $faker);
                            $priceCents  = $this->priceFor($secName, $itemName, $faker);

                            $secLower = Str::lower($secName);
                            $prepMinutes = (
                                str_contains($secLower, 'pizza')
                                || str_contains($secLower, 'hambúrguer')
                                || str_contains($secLower, 'pratos')
                                || str_contains($secLower, 'sushi')
                                || str_contains($secLower, 'massas')
                                || str_contains($secLower, 'porções')
                                || str_contains($secLower, 'marmitas')
                            ) ? $faker->numberBetween(10, 30) : null;

                            $item = \App\Models\MenuItem::create([
                                'business_id'       => $business->id,
                                'section_id'        => $section->id,
                                'business_loc_id'   => null,
                                'name'              => $itemName,
                                'description'       => $faker->sentence(10),
                                'price_cents'       => $priceCents,
                                'prep_time_minutes' => $prepMinutes,
                                'tags'              => $faker->randomElements(
                                    ['artesanal','combo','promo','vegano','sem glúten','família','individual'],
                                    $faker->numberBetween(0,2)
                                ),
                                'is_available'      => $faker->boolean(94),
                                'image_url'         => $faker->boolean(55)
                                    ? $faker->imageUrl(800, 600, 'food', true)
                                    : null,
                                'sort_order'        => $i * 10,
                            ]);

                            // Opções
                            if ($prepMinutes) {
                                foreach ($this->optionsFor($secName) as $opt) {
                                    \App\Models\MenuItemOption::create([
                                        'item_id'           => $item->id,
                                        'group_name'        => $opt['group'],
                                        'name'              => $opt['name'],
                                        'price_delta_cents' => $opt['delta'],
                                        'max_select'        => $opt['max'] ?? null,
                                    ]);
                                }
                            }

                            $businessItemsCounter++;
                        }
                    }
                }

                $business->update(['items_count' => $businessItemsCounter]);
            }
        }

        $this->command->info('Seed de empresas e cardápios concluído com sucesso (senha: 123).');
    }

    /** Define nomes de negócio focados em delivery */
    private function randomBusinessName($faker): string
    {
        $prefix = $faker->randomElement([
            'Pizzaria', 'Hamburgueria', 'Lanchonete', 'Sushi Bar', 'Temakeria',
            'Marmitaria', 'Churrascaria', 'Pastelaria', 'Padaria', 'Cafeteria',
            'Açaíteria', 'Massas & Molhos', 'Comida Caseira'
        ]);
        $suffix = $faker->randomElement([
            'do Centro', 'da Praça', 'Express', 'Gourmet', 'Delivery',
            'Premium', 'Urbana', 'Caiçara', 'da Ilha'
        ]);
        return "{$prefix} {$suffix}";
    }

    /** Escolhe categorias coerentes com o tipo de negócio */
    /** Escolhe categorias coerentes com o tipo de negócio (busca por keyword em nome/slug) */
    private function pickCategoriesForBusiness($categories, string $bizName, $faker): array
    {
        $bizLower = Str::lower($bizName);

        // Palavras-chave que queremos casar nas categorias já criadas
        // (lado direito são ARRAYS de keywords; usamos "contains" no nome/slug)
        $wantedByType = [
            'pizzaria'       => ['pizza','porcoes','acompanhamentos','refrigerantes','sucos','bebidas','doces','sobremesas'],
            'hamburgueria'   => ['hamburguer','lanche','porcoes','acompanhamentos','refrigerantes','sucos','cerveja','drinks','sobremesa','doces'],
            'lanchonete'     => ['lanche','salgados','porcoes','acompanhamentos','refrigerantes','sucos','doces'],
            'sushi'          => ['sushi','sashimi','temaki','combos','orientais','bebidas','refrigerantes','sucos'],
            'temakeria'      => ['temaki','sushi','sashimi','combos','orientais','bebidas'],
            'marmitaria'     => ['marmita','pratos-do-dia','caseira','porcoes','acompanhamentos','refrigerantes','sucos','sobremesas'],
            'comida caseira' => ['pratos-do-dia','caseira','marmita','porcoes','acompanhamentos','sobremesas','bebidas'],
            'churrascaria'   => ['churrasco','grelhados','porcoes','acompanhamentos','bebidas','sobremesas'],
            'pastelaria'     => ['pasteis','salgados','porcoes','acompanhamentos','bebidas','doces'],
            'padaria'        => ['padaria','confeitaria','sanduiches','cafes','doces'],
            'cafeteria'      => ['cafes','quentes','sanduiches','doces','bebidas'],
            'açaíteria'      => ['acai','sorvetes','sobremesas','bebidas'],
            'massas'         => ['massas','molhos','assados','porcoes','acompanhamentos','bebidas','doces'],
        ];

        // Escolhe a lista de keywords pelo tipo de negócio no nome
        $keywords = collect($wantedByType)
            ->first(function ($_, $key) use ($bizLower) {
                return Str::contains($bizLower, $key);
            }) ?? ['porcoes','acompanhamentos','bebidas','doces']; // fallback genérico

        // Normalizador para comparar sem acentos/espaços
        $norm = fn ($s) => Str::slug(Str::lower($s));

        // Procura nas categorias existentes por NOME ou SLUG que contenham as keywords
        $selectedIds = [];
        foreach ($keywords as $kw) {
            $kw = $norm($kw);
            $match = $categories->first(function ($cat) use ($kw, $norm) {
                return Str::contains($norm($cat->name), $kw) || Str::contains($norm($cat->slug), $kw);
            });
            if ($match) {
                $selectedIds[] = $match->id;
            }
        }

        // Se nada casou, escolhe 2–4 aleatórias pra não ficar vazio
        if (empty($selectedIds)) {
            $selectedIds = $categories->pluck('id')->shuffle()->take($faker->numberBetween(2,4))->all();
        }

        return array_values(array_unique($selectedIds));
    }


    /** Seções de cardápio por tipo */
    private function sectionsForBusiness(string $bizName): array
    {
        $lower = Str::lower($bizName);

        if (str_contains($lower,'pizzaria')) {
            return ['Pizzas Salgadas','Pizzas Doces','Porções & Acompanhamentos','Bebidas'];
        }
        if (str_contains($lower,'hamburgueria')) {
            return ['Hambúrgueres Artesanais','Combos','Porções & Acompanhamentos','Bebidas','Sobremesas'];
        }
        if (str_contains($lower,'lanchonete')) {
            return ['Lanches','Salgados','Porções & Acompanhamentos','Bebidas','Doces'];
        }
        if (str_contains($lower,'sushi') || str_contains($lower,'temakeria')) {
            return ['Sushi & Sashimi','Temakis','Combos Orientais','Porções & Acompanhamentos','Bebidas'];
        }
        if (str_contains($lower,'marmitaria') || str_contains($lower,'comida caseira')) {
            return ['Pratos do Dia','Marmitas','Porções & Acompanhamentos','Bebidas','Sobremesas'];
        }
        if (str_contains($lower,'churrascaria')) {
            return ['Carnes na Brasa','Pratos','Porções & Acompanhamentos','Bebidas','Sobremesas'];
        }
        if (str_contains($lower,'pastelaria')) {
            return ['Pastéis','Salgados','Porções & Acompanhamentos','Bebidas','Doces'];
        }
        if (str_contains($lower,'padaria') || str_contains($lower,'cafeteria')) {
            return ['Cafés & Quentes','Sanduíches','Salgados','Doces','Bebidas'];
        }
        if (str_contains($lower,'açaí')) {
            return ['Açaí no Copo','Tigelas & Combos','Bebidas','Sobremesas'];
        }
        if (str_contains($lower,'massa')) {
            return ['Massas','Molhos & Assados','Porções & Acompanhamentos','Bebidas','Sobremesas'];
        }

        // fallback genérico delivery
        return ['Lanches','Porções & Acompanhamentos','Bebidas','Sobremesas'];
    }

    /** Gera itens plausíveis por seção */
    private function randomItemName(string $section, $faker): string
    {
        $map = [
            // Pizzaria
            'Pizzas Salgadas'           => ['Calabresa','Mussarela','Portuguesa','Frango com Catupiry','Marguerita','Quatro Queijos','Napolitana'],
            'Pizzas Doces'              => ['Chocolate com Morango','Romeu e Julieta','Banana com Canela','Brigadeiro'],
            // Hamburgueria / Lanches
            'Hambúrgueres Artesanais'   => ['Classic Burger','Cheddar Bacon','Smash Duplo','Costela BBQ','Frango Crispy'],
            'Combos'                    => ['Combo Classic','Combo Cheddar','Combo Frango','Combo Família'],
            'Lanches'                   => ['X-Salada','X-Bacon','X-Tudo','Misto Quente','Bauru'],
            // Orientais
            'Sushi & Sashimi'           => ['Combo Sashimi 20un','Uramaki Califórnia 16un','Niguiri Salmão 8un','Hot Roll 10un'],
            'Temakis'                    => ['Temaki Salmão','Temaki Atum','Temaki Skin','Temaki Califórnia'],
            'Combos Orientais'          => ['Combo Família 40un','Combo Duo 24un','Combo Premium 60un'],
            // Caseiros / Marmita / Churrasco
            'Pratos do Dia'             => ['Parmegiana de Frango','Bife Acebolado','Strogonoff de Carne','Feijoada'],
            'Marmitas'                  => ['Marmita Média','Marmita Grande','Marmita Fit'],
            'Carnes na Brasa'           => ['Picanha na Chapa','Fraldinha','Linguiça Artesanal','Costela Suína'],
            'Pratos'                    => ['Arroz, Feijão, Farofa e Carne','Parmegiana com Fritas','Frango Grelhado'],
            // Pastel / Padaria / Café / Açaí / Massas
            'Pastéis'                   => ['Pastel de Carne','Pastel de Queijo','Pastel de Pizza','Pastel de Palmito'],
            'Salgados'                  => ['Coxinha','Esfiha','Kibe','Enroladinho de Salsicha'],
            'Cafés & Quentes'           => ['Café Expresso','Cappuccino','Mocha','Chá'],
            'Sanduíches'                => ['Pão na Chapa','Misto Quente','Queijo Quente','Croissant Presunto e Queijo'],
            'Doces'                     => ['Brownie','Pudim','Torta de Limão','Brigadeiro'],
            'Açaí no Copo'              => ['Açaí 300ml','Açaí 500ml','Açaí 700ml'],
            'Tigelas & Combos'          => ['Tigela Açaí Frutas','Açaí com Granola','Combo Açaí Família'],
            'Massas'                    => ['Spaghetti à Bolonhesa','Fettuccine Alfredo','Lasanha Bolonhesa','Rondelli Quatro Queijos'],
            'Molhos & Assados'          => ['Molho Bolonhesa','Molho Branco','Lasanha de Frango'],
            // Comuns
            'Porções & Acompanhamentos' => ['Batata Frita','Onion Rings','Polenta Frita','Arroz & Feijão','Farofa','Vinagrete'],
            'Bebidas'                   => ['Água Mineral','Refrigerante Lata','Refrigerante 2L','Suco Natural 300ml','Cerveja Long Neck'],
            'Sobremesas'                => ['Pudim','Mousse de Chocolate','Açaí na Tigela','Brownie com Sorvete'],
        ];

        $base = $map[$section] ?? [$faker->words(2, true)];
        return $faker->randomElement($base);
    }

    /** Precificação básica por seção (R$ em centavos) */
    private function priceFor(string $section, string $itemName, $faker): int
    {
        $p = fn($min,$max)=>$faker->numberBetween($min,$max);

        return match ($section) {
            // pizzas
            'Pizzas Salgadas'           => $p(2990, 6990),
            'Pizzas Doces'              => $p(2990, 6990),
            // burger / lanches
            'Hambúrgueres Artesanais'   => $p(2490, 4990),
            'Combos'                    => $p(3990, 7990),
            'Lanches'                   => $p(1990, 3990),
            // orientais
            'Sushi & Sashimi'           => $p(2990, 9990),
            'Temakis'                   => $p(2490, 6990),
            'Combos Orientais'          => $p(5990, 15990),
            // caseiros / churrasco
            'Pratos do Dia'             => $p(2490, 4990),
            'Marmitas'                  => $p(1990, 4490),
            'Carnes na Brasa'           => $p(4990, 14990),
            'Pratos'                    => $p(2990, 5990),
            // pastel / padaria / café / açaí / massas
            'Pastéis'                   => $p(1200, 2500),
            'Salgados'                  => $p(900,  2000),
            'Cafés & Quentes'           => $p(600,  1800),
            'Sanduíches'                => $p(1200, 2800),
            'Doces'                     => $p(900,  2500),
            'Açaí no Copo'              => $p(1500, 3500),
            'Tigelas & Combos'          => $p(2500, 6500),
            'Massas'                    => $p(2490, 6990),
            'Molhos & Assados'          => $p(1490, 4990),
            // comuns
            'Porções & Acompanhamentos' => $p(1490, 3990),
            'Bebidas'                   => $p(600,  1590),
            'Sobremesas'                => $p(1200, 3500),
            default                     => $p(1990, 6990),
        };
    }

    /** Opções por seção (tamanhos/adicionais/combos) */
    private function optionsFor(string $sec): array
    {
        $pizza = [
            ['group'=>'Tamanho','name'=>'Broto','delta'=>0],
            ['group'=>'Tamanho','name'=>'Média','delta'=>1500],
            ['group'=>'Tamanho','name'=>'Grande','delta'=>3000],
            ['group'=>'Tamanho','name'=>'Família','delta'=>5000],
            ['group'=>'Borda','name'=>'Catupiry','delta'=>800],
            ['group'=>'Borda','name'=>'Cheddar','delta'=>800],
        ];
        $burger = [
            ['group'=>'Pão','name'=>'Tradicional','delta'=>0],
            ['group'=>'Pão','name'=>'Brioche','delta'=>400],
            ['group'=>'Extra','name'=>'Bacon','delta'=>500],
            ['group'=>'Extra','name'=>'Queijo','delta'=>300],
            ['group'=>'Ponto da Carne','name'=>'Ao ponto','delta'=>0],
            ['group'=>'Ponto da Carne','name'=>'Bem passado','delta'=>0],
        ];
        $combo = [
            ['group'=>'Tamanho','name'=>'Individual','delta'=>0],
            ['group'=>'Tamanho','name'=>'Duplo','delta'=>1800],
            ['group'=>'Tamanho','name'=>'Família','delta'=>4200],
        ];
        $oriental = [
            ['group'=>'Tamanho','name'=>'8 un','delta'=>0],
            ['group'=>'Tamanho','name'=>'16 un','delta'=>1500],
            ['group'=>'Tamanho','name'=>'24 un','delta'=>3200],
        ];
        $marmita = [
            ['group'=>'Tamanho','name'=>'Média','delta'=>0],
            ['group'=>'Tamanho','name'=>'Grande','delta'=>800],
            ['group'=>'Acompanhamentos','name'=>'Ovo Frito','delta'=>300],
            ['group'=>'Acompanhamentos','name'=>'Salada','delta'=>0],
        ];
        $porcao = [
            ['group'=>'Porção','name'=>'Pequena','delta'=>0],
            ['group'=>'Porção','name'=>'Grande','delta'=>900],
        ];
        $bebidas = [
            ['group'=>'Tamanho','name'=>'300 ml','delta'=>0],
            ['group'=>'Tamanho','name'=>'500 ml','delta'=>300],
            ['group'=>'Tamanho','name'=>'1 L','delta'=>700],
            ['group'=>'Gelo','name'=>'Com gelo','delta'=>0],
        ];

        return match (true) {
            str_contains($sec,'Pizza')                           => $pizza,
            str_contains($sec,'Hambúrguer') || str_contains($sec,'Lanche') => $burger,
            str_contains($sec,'Combos')                          => $combo,
            str_contains($sec,'Sushi') || str_contains($sec,'Temaki')      => $oriental,
            str_contains($sec,'Marmita') || str_contains($sec,'Pratos')    => $marmita,
            str_contains($sec,'Porções')                         => $porcao,
            str_contains($sec,'Bebidas')                         => $bebidas,
            default                                              => $porcao,
        };
    }
}
