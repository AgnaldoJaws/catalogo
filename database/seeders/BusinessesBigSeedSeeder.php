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

        $cities     = City::pluck('id','slug');
        $categories = Category::all();
        $owners     = User::where('role','owner')->pluck('id')->all();

        if ($cities->isEmpty() || $categories->isEmpty() || empty($owners)) {
            $this->command->warn("Execute CitiesValeDoRibeiraSeeder, CategoriesSeeder e DemoOwnersSeeder antes.");
            return;
        }

        $latMin = -25.5; $latMax = -23.9;
        $lngMin = -49.2; $lngMax = -46.8;

        foreach ($cities as $citySlug => $cityId) {
            $bizCount = $faker->numberBetween(8, 14);

            for ($b=0; $b<$bizCount; $b++) {
                $bizName = $this->randomBusinessName($faker);

                $legumeImages = [
                    'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce', // tomates
                    'https://images.unsplash.com/photo-1572441710534-6801ad2c92b9', // cenouras
                    'https://images.unsplash.com/photo-1582515073490-dc0c5f9bcd33', // alface
                    'https://images.unsplash.com/photo-1603046891685-d6e08b1dca1f', // cebolas
                    'https://images.unsplash.com/photo-1584305574646-9e01e05fa1a8', // batatas
                    'https://images.unsplash.com/photo-1590080875839-78c4fe6f16df', // couve
                ];

                $business = Business::create([
                    'user_id'     => $faker->randomElement($owners),
                    'name'        => $bizName,
                    'slug'        => Str::slug($bizName.' '.$citySlug.' '.Str::random(4)),
                    'logo_url'    => $faker->randomElement($legumeImages),
                    'about'       => $faker->randomElement([
                        'Produção familiar com foco em sustentabilidade.',
                        'Produtos cultivados com respeito à natureza.',
                        'Agroindústria artesanal da nossa região.',
                        'Do campo direto para sua mesa, com qualidade e amor.',
                    ]),
                    'avg_rating'  => $faker->randomFloat(1, 4.3, 5.0),
                    'items_count' => 0,
                    'status'      => 1,
                ]);

                $userEmpresa = \App\Models\User::updateOrCreate(
                    ['email' => 'empresa+' . $business->slug . '@catalogo.local'],
                    [
                        'name'              => 'Produtor ' . $business->name,
                        'password'          => Hash::make('123'),
                        'role'              => 'empresa',
                        'phone'             => '13' . str_pad((string)rand(90000000,99999999), 8, '0', STR_PAD_LEFT),
                        'email_verified_at' => now(),
                    ]
                );

                $business->user_id = $userEmpresa->id;
                $business->save();

                // Categorias agrícolas
                $attach = $this->pickCategoriesForBusiness($categories, $bizName, $faker);
                $business->categories()->sync($attach);

                // Localização (sítio, chácara, feira)
                $locCount = $faker->numberBetween(1,2);
                $businessItemsCounter = 0;

                for ($l=0; $l<$locCount; $l++) {
                    $lat = $faker->randomFloat(6, $latMin, $latMax);
                    $lng = $faker->randomFloat(6, min($lngMin,$lngMax), max($lngMin,$lngMax));

                    $location = BusinessLocation::create([
                        'business_id' => $business->id,
                        'city_id'     => $cityId,
                        'name'        => $faker->randomElement(['Sítio ' . $faker->lastName(), 'Feira Municipal', 'Chácara ' . $faker->firstName()]),
                        'address'     => $faker->streetAddress(),
                        'lat'         => $lat,
                        'lng'         => $lng,
                        'whatsapp'    => '55'.$faker->numerify('13########'),
                        'phone'       => '55'.$faker->numerify('13########'),
                        'status'      => 1,
                    ]);

                    // Horários
                    for ($w=0; $w<7; $w++) {
                        BusinessHour::create([
                            'business_loc_id' => $location->id,
                            'weekday'         => $w,
                            'open_time'       => '07:00:00',
                            'close_time'      => '18:00:00',
                            'overnight'       => false,
                        ]);
                    }

                    // Seções do catálogo rural
                    $sections = ['Hortaliças & Verduras','Frutas Frescas','Produtos Orgânicos','Laticínios & Ovos','Mel & Derivados','Artesanato & Naturais'];
                    $sort = 10;

                    foreach ($sections as $secName) {
                        $section = MenuSection::create([
                            'business_id'     => $business->id,
                            'business_loc_id' => null,
                            'name'            => $secName,
                            'sort_order'      => $sort,
                        ]);
                        $sort += 10;

                        $itemCount = $faker->numberBetween(5, 10);
                        for ($i=1; $i<=$itemCount; $i++) {
                            $itemName = $this->randomItemName($secName, $faker);
                            $priceCents = $this->priceFor($secName, $faker);

                            $item = MenuItem::create([
                                'business_id'       => $business->id,
                                'section_id'        => $section->id,
                                'business_loc_id'   => null,
                                'name'              => $itemName,
                                'description'       => $faker->sentence(8),
                                'price_cents'       => $priceCents,
                                'is_available'      => $faker->boolean(95),
                                'image_url'         => $faker->imageUrl(800, 600, 'vegetable', true),
                                'sort_order'        => $i * 10,
                            ]);

                            $businessItemsCounter++;
                        }
                    }
                }

                $business->update(['items_count' => $businessItemsCounter]);
            }
        }

        $this->command->info('Seed de produtores e agroindústrias concluído com sucesso (senha: 123).');
    }

    private function randomBusinessName($faker): string
    {
        $prefix = $faker->randomElement([
            'Sítio', 'Chácara', 'Fazenda', 'Agro', 'Horta', 'Produtora', 'Cooperativa', 'Empório'
        ]);
        $suffix = $faker->randomElement([
            'Verde Vivo', 'Boa Terra', 'Orgânicos do Vale', 'Raízes da Mata', 'Flor do Campo', 'Sabor da Roça', 'Vale Natural'
        ]);
        return "{$prefix} {$suffix}";
    }

    private function pickCategoriesForBusiness($categories, string $bizName, $faker): array
    {
        $keywords = ['hortaliças','frutas','legumes','grãos','orgânicos','laticínios','mel','artesanato','plantas','temperos'];
        $norm = fn($s)=>Str::slug(Str::lower($s));

        $selectedIds = [];
        foreach ($keywords as $kw) {
            $match = $categories->first(fn($cat)=>Str::contains($norm($cat->name), $kw));
            if ($match) $selectedIds[] = $match->id;
        }

        return array_values(array_unique($selectedIds ?: $categories->pluck('id')->shuffle()->take(3)->all()));
    }

    private function randomItemName(string $section, $faker): string
    {
        $map = [
            'Hortaliças & Verduras' => ['Alface Crespa','Couve','Rúcula','Cebolinha','Salsa','Espinafre'],
            'Frutas Frescas'        => ['Banana Nanica','Laranja Lima','Mamão Formosa','Abacaxi Pérola','Manga Palmer'],
            'Produtos Orgânicos'    => ['Tomate Orgânico','Abobrinha Orgânica','Cenoura Orgânica','Batata Doce'],
            'Laticínios & Ovos'     => ['Leite Integral','Queijo Minas Frescal','Iogurte Natural','Ovos Caipiras'],
            'Mel & Derivados'       => ['Mel Puro','Pólen Apícola','Própolis','Cera de Abelha'],
            'Artesanato & Naturais' => ['Sabonete Vegetal','Velas Artesanais','Xampu Natural','Ervas Desidratadas'],
        ];

        return $faker->randomElement($map[$section] ?? [$faker->word()]);
    }

    private function priceFor(string $section, $faker): int
    {
        $p = fn($min,$max)=>$faker->numberBetween($min,$max);

        return match ($section) {
            'Hortaliças & Verduras' => $p(300, 800),
            'Frutas Frescas'        => $p(500, 1500),
            'Produtos Orgânicos'    => $p(800, 2500),
            'Laticínios & Ovos'     => $p(1000, 3000),
            'Mel & Derivados'       => $p(1500, 5000),
            'Artesanato & Naturais' => $p(2000, 6000),
            default                 => $p(1000, 4000),
        };
    }
}
