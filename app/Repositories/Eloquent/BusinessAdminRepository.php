<?php

namespace App\Repositories\Eloquent;

use App\Models\{Business, BusinessLocation, MenuItem, MenuSection};
use App\Repositories\Contracts\BusinessAdminRepositoryInterface;

class BusinessAdminRepository implements BusinessAdminRepositoryInterface
{
    public function find(int $id): ?Business {
        return Business::withCount('items')->find($id);
    }

    public function updateProfile(int $id, array $data): bool {
        $biz = Business::findOrFail($id);
        $biz->fill([
            'name'       => $data['name'] ?? $biz->name,
            'about'      => $data['about'] ?? $biz->about,
            'whatsapp'   => $data['whatsapp'] ?? $biz->whatsapp,
            'instagram'  => $data['instagram'] ?? $biz->instagram ?? null,
            'facebook'   => $data['facebook'] ?? $biz->facebook ?? null,
        ]);
        return $biz->save();
    }

    public function listLocations(int $businessId): array {
        return BusinessLocation::where('business_id', $businessId)
            ->orderBy('id')->get()->toArray();
    }

    public function upsertLocation(int $businessId, array $data): int {
        $loc = isset($data['id'])
            ? BusinessLocation::where('business_id', $businessId)->findOrFail($data['id'])
            : new BusinessLocation(['business_id' => $businessId]);

        $loc->fill([
            'city_id'   => $data['city_id'] ?? $loc->city_id,
            'address'   => $data['address'] ?? $loc->address,
            'lat'       => $data['lat'] ?? $loc->lat,
            'lng'       => $data['lng'] ?? $loc->lng,
            'status'    => $data['status'] ?? $loc->status ?? 1,
            'phone'     => $data['phone'] ?? $loc->phone ?? null,
            'whatsapp'  => $data['whatsapp'] ?? $loc->whatsapp ?? null,
        ]);
        $loc->save();

        return (int) $loc->id;
    }

    public function deleteLocation(int $businessId, int $locationId): bool {
        return (bool) BusinessLocation::where('business_id',$businessId)->where('id',$locationId)->delete();
    }

    public function listMenuSections(int $businessId): array {
        return MenuSection::where('business_id',$businessId)->orderBy('sort_order')->get()->toArray();
    }

    public function createMenuSection(int $businessId, array $data): int {
        $sec = new MenuSection([
            'business_id' => $businessId,
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        $sec->save();
        return (int) $sec->id;
    }

    public function updateMenuSection(int $sectionId, array $data): bool {
        $sec = MenuSection::findOrFail($sectionId);
        $sec->fill([
            'name' => $data['name'] ?? $sec->name,
            'sort_order' => $data['sort_order'] ?? $sec->sort_order,
        ]);
        return $sec->save();
    }

    public function deleteMenuSection(int $sectionId): bool {
        // cascade delete dos itens
        MenuItem::where('section_id',$sectionId)->delete();
        return (bool) MenuSection::where('id',$sectionId)->delete();
    }

    public function listMenuItems(int $sectionId): array
    {
        return MenuItem::where('section_id', $sectionId)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($it) {
                return [
                    'id'          => $it->id,
                    'name'        => $it->name,
                    'description' => $it->description,
                    'price'       => $it->price_cents,          // normaliza
                    'img_url'     => $it->image_url,
                    'prep_min'    => $it->prep_time_minutes,
                    'tags'        => is_array($it->tags) ? implode(',', $it->tags) : $it->tags,
                    'is_available'=> (bool) $it->is_available,
                    'sort_order'  => $it->sort_order,
                ];
            })
            ->toArray();
    }


    public function createMenuItem(int $sectionId, array $data): int {
        $it = new MenuItem([
            'section_id' => $sectionId,
            'name'       => $data['name'],
            'description'=> $data['description'] ?? null,
            'price'      => $data['price'] ?? 0,
            'img_url'    => $data['img_url'] ?? null,
            'is_available' => array_key_exists('is_available',$data) ? (int)$data['is_available'] : 1,
            'sort_order' => $data['sort_order'] ?? 0,
            'prep_min'   => $data['prep_min'] ?? 0,
            'tags'       => $data['tags'] ?? null,
        ]);
        $it->save();
        return (int) $it->id;
    }

    public function updateMenuItem(int $itemId, array $data): bool {
        $it = MenuItem::findOrFail($itemId);
        $it->fill([
            'name'       => $data['name'] ?? $it->name,
            'description'=> $data['description'] ?? $it->description,
            'price'      => $data['price'] ?? $it->price,
            'img_url'    => $data['img_url'] ?? $it->img_url,
            'is_available' => array_key_exists('is_available',$data) ? (int)$data['is_available'] : $it->is_available,
            'sort_order' => $data['sort_order'] ?? $it->sort_order,
            'prep_min'   => $data['prep_min'] ?? $it->prep_min,
            'tags'       => $data['tags'] ?? $it->tags,
        ]);
        return $it->save();
    }

    public function deleteMenuItem(int $itemId): bool {
        return (bool) MenuItem::where('id',$itemId)->delete();
    }
}
