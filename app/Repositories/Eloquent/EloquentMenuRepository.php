<?php

namespace App\Repositories\Eloquent;

use App\Models\Business;
use App\Models\MenuSection;
use App\Repositories\Contracts\MenuRepositoryInterface;

class EloquentMenuRepository implements MenuRepositoryInterface
{
    public function getMenu(string $businessSlug, ?int $locationId = null): array
    {
        $business = Business::where('slug', $businessSlug)->first();
        if (!$business) {
            return ['business' => null, 'sections' => []];
        }

        // 1) tenta seções/itens específicos da filial
        $sectionsLoc = collect();
        if ($locationId) {
            $sectionsLoc = MenuSection::with(['items' => fn($q) => $q->available()->orderBy('sort_order')])
                ->where('business_loc_id', $locationId)
                ->orderBy('sort_order')
                ->get();
        }

        // 2) se vazio, herda da marca
        $sectionsBrand = MenuSection::with(['items' => fn($q) => $q->available()->orderBy('sort_order')])
            ->where('business_id', $business->id)
            ->whereNull('business_loc_id')
            ->orderBy('sort_order')
            ->get();

        $sections = $sectionsLoc->count() ? $sectionsLoc : $sectionsBrand;

        // Mapeia estrutura enxuta para retorno
        $resultSections = $sections->map(function ($sec) {

            return [
                'id'     => $sec->id,
                'name'   => $sec->name,
                'items'  => $sec->items->map(fn($i) => [
                    'id'        => $i->id,
                    'name'      => $i->name,
                    'desc'      => $i->description,
                    'price'     => $i->price_cents,
                    'img'       => $i->image_src,
                    'tags'      => $i->tags,
                    'prep_min'  => $i->prep_time_minutes,
                ])->values(),
            ];
        })->values()->all();

        return [
            'business' => [
                'id'   => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'sections' => $resultSections,
        ];
    }
}
