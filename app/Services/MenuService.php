<?php

namespace App\Services;

use App\Services\Contracts\MenuServiceInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class MenuService implements MenuServiceInterface
{
    public function __construct(
        private readonly MenuRepositoryInterface $menuRepo
    ) {}

    public function getMenu(string $businessSlug, ?int $locationId = null): array
    {
        $data = $this->menuRepo->getMenu($businessSlug, $locationId);

        // Normaliza "business" para array simples
        $business = $this->normalizeBusiness(Arr::get($data, 'business'));

        // Normaliza "sections" e "items" aceitando arrays, models ou collections
        $sections = collect(Arr::get($data, 'sections', []))
            ->map(function ($sec) {
                // $sec pode ser array ou Model
                $secArr = $this->toArray($sec);

                $items = collect(Arr::get($secArr, 'items', []))
                    ->map(function ($item) {
                        $it = $this->toArray($item);

                        return [
                            'id'       => (int)    Arr::get($it, 'id'),
                            'name'     => (string) Arr::get($it, 'name', ''),
                            'desc'     => (string) (Arr::get($it, 'description') ?? Arr::get($it, 'desc', '')),
                            // aceita price em centavos (int) ou decimal (string/float)
                            'price'    => $this->normalizePrice(Arr::get($it, 'price')),
                            'img'      => Arr::get($it, 'image_url') ?? Arr::get($it, 'img'),
                            'tags'     => (array)  (Arr::get($it, 'tags', []) ?: []),
                            'prep_min' => Arr::get($it, 'prep_min'),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id'    => (int)    Arr::get($secArr, 'id'),
                    'name'  => (string) (Arr::get($secArr, 'name') ?? Arr::get($secArr, 'title', '')),
                    'items' => $items,
                ];
            })
            ->values()
            ->all();

        return [
            'business' => $business,
            'sections' => $sections,
        ];
    }

    // -------- helpers --------

    /** Converte arrays | models | collections em array puro */
    private function toArray($value): array
    {
        if ($value instanceof Collection) {
            return $value->toArray();
        }
        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }
        return (array) $value;
    }

    private function normalizeBusiness($business): array
    {
        $b = $this->toArray($business);

        return [
            'id'          => (int)    Arr::get($b, 'id'),
            'name'        => (string) Arr::get($b, 'name', ''),
            'slug'        => (string) (Arr::get($b, 'slug') ?? Str::slug(Arr::get($b, 'name', ''))),
            'logo_url'    => Arr::get($b, 'logo_url') ?? Arr::get($b, 'logo'),
            'about'       => Arr::get($b, 'about') ?? Arr::get($b, 'description'),
            'avg_rating'  => (float)  (Arr::get($b, 'avg_rating', 0)),
            'items_count' => (int)    (Arr::get($b, 'items_count', 0)),
        ];
    }

    /**
     * Normaliza preço:
     * - se vier "3590" (centavos) mantém como int
     * - se vier "35.90" (decimal) converte para centavos (int)
     */
    private function normalizePrice($price): int
    {
        if (is_null($price) || $price === '') return 0;

        // já em centavos (int) → retorna
        if (is_int($price)) return $price;

        // string/float decimal → centavos
        $num = (float) str_replace(',', '.', (string) $price);
        return (int) round($num * 100);
    }
}
