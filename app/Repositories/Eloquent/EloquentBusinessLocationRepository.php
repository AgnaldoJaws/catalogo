<?php

namespace App\Repositories\Eloquent;

use App\Models\BusinessLocation;
use App\Repositories\Contracts\BusinessLocationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentBusinessLocationRepository implements BusinessLocationRepositoryInterface
{
    public function search(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $citySlug     = $filters['city_slug']     ?? null;
        $categorySlug = $filters['category_slug'] ?? null;
        $q            = $filters['q']             ?? null;
        $lat          = isset($filters['lat']) && $filters['lat'] !== '' ? (float) $filters['lat'] : null;
        $lng          = isset($filters['lng']) && $filters['lng'] !== '' ? (float) $filters['lng'] : null;
        $radiusKm     = isset($filters['radius_km']) ? max(0.1, (float) $filters['radius_km']) : 5.0;
        $openNow      = !empty($filters['open_now']);
        $sort         = $filters['sort'] ?? 'nearest';

        $query = BusinessLocation::query()
            ->select('business_locations.*')
            ->join('businesses', 'businesses.id', '=', 'business_locations.business_id')
            ->with([
                'business:id,name,slug,avg_rating,items_count,status,logo_path',
                'city:id,name,slug',
            ]);

        if ($citySlug) {
            $query->whereHas('city', fn($c) => $c->where('slug', $citySlug));
        }

        if ($categorySlug) {
            $query->whereHas('business.categories', fn($c) => $c->where('slug', $categorySlug));
        }

        if ($q) {
            $like = '%'.str_replace(' ', '%', $q).'%';
            $query->where(function ($w) use ($like) {
                $w->whereHas('business', fn($b) => $b->where('name', 'like', $like))
                    ->orWhere('business_locations.address', 'like', $like)
                    ->orWhereHas('business.items', function ($i) use ($like) {
                        $i->where('name', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        if ($openNow) {
            // adaptar quando tiver tabela de horários
            // $query->whereHas('hours', ...);
        }

        $hasGeo = is_numeric($lat) && is_numeric($lng);

        // 🔧 Distância e ordenação (SEM setBindings!)
        if ($hasGeo && $sort === 'nearest') {
            // Haversine com bind seguro
            $distanceSql = "(6371 * acos( cos(radians(?)) * cos(radians(business_locations.lat)) * cos(radians(business_locations.lng) - radians(?)) + sin(radians(?)) * sin(radians(business_locations.lat)) ))";

            $query->selectRaw("$distanceSql AS distance_km", [$lat, $lng, $lat])
                ->whereNotNull('business_locations.lat')
                ->whereNotNull('business_locations.lng')
                ->having('distance_km', '<=', $radiusKm)
                ->orderBy('distance_km');
        } else {
            switch ($sort) {
                case 'rating':
                    $query->orderByDesc('businesses.avg_rating');
                    break;
                case 'az':
                    $query->orderBy('businesses.name');
                    break;
                case 'za':
                    $query->orderByDesc('businesses.name');
                    break;
                case 'items':
                    $query->orderByDesc('businesses.items_count');
                    break;
                default:
                    $query->orderByDesc('business_locations.created_at');
            }
        }

        // Em cenário normal não duplica, pois o join é 1:1.
        // Se notar duplicatas por conta de whereHas, pode manter:
        // $query->groupBy('business_locations.id');

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?BusinessLocation
    {
        return BusinessLocation::with(['business', 'city', 'hours'])->find($id);
    }

    public function listByBusiness(int $businessId): Collection
    {
        return BusinessLocation::where('business_id', $businessId)
            ->with('city:id,name,slug')
            ->orderBy('id')
            ->get();
    }
}
