<?php

namespace App\Services;

use App\Repositories\Contracts\BusinessLocationRepositoryInterface;
use App\Services\Contracts\LocationSearchServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationSearchService implements LocationSearchServiceInterface
{
    public function __construct(
        private readonly BusinessLocationRepositoryInterface $locations
    ) {}

    public function search(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        // Valores seguros (evita Undefined array key)
        $lat = $filters['lat'] ?? null;
        $lng = $filters['lng'] ?? null;
        $hasGeo = is_numeric($lat) && is_numeric($lng);

        // Raio com clamp (1..25)
        $filters['radius_km'] = isset($filters['radius_km'])
            ? max(1, min((float) $filters['radius_km'], 25))
            : 5.0;

        // Sort default: nearest se tiver geo, senão rating
        if (empty($filters['sort'])) {
            $filters['sort'] = $hasGeo ? 'nearest' : 'rating';
        }

        // Se não tem geo válido, remove para o repo não tentar distância
        if (!$hasGeo) {
            unset($filters['lat'], $filters['lng']);
        }

        // Busca no repositório
        $page = $this->locations->search($filters, $perPage);

        // Transform leve para o front
        $page->getCollection()->transform(function ($loc) {
            return [
                'location_id'  => $loc->id,
                'business_id'  => $loc->business->id,
                'business'     => [
                    'name'        => $loc->business->name,
                    'slug'        => $loc->business->slug,
                    'avg_rating'  => (float) ($loc->business->avg_rating ?? 0),
                    'items_count' => (int)   ($loc->business->items_count ?? 0),
                ],
                'city'         => $loc->city?->name,
                'address'      => $loc->address,
                'distance_km'  => isset($loc->distance_km) ? (float) $loc->distance_km : null,
                'is_open_now'  => null,
                'whatsapp'     => $loc->whatsapp,
                'thumb_url'    => $loc->business->logo_url ?? null,
            ];
        });

        return $page;
    }
}
