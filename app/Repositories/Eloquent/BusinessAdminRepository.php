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
            'name'      => $data['name']      ?? $biz->name,
            'slug'      => $data['slug']      ?? $biz->slug,
            'about'     => $data['about']     ?? $biz->about,
            'whatsapp'  => $data['whatsapp']  ?? $biz->whatsapp,
            'instagram' => $data['instagram'] ?? $biz->instagram,
            'facebook'  => $data['facebook']  ?? $biz->facebook,
            'logo_url'  => $data['logo_url']  ?? $biz->logo_url,
        ]);

        if (!empty($data['logo_path'])) {
            $biz->logo_path = $data['logo_path'];
        }

        return $biz->save();
    }


    public function listLocations(int $businessId): array {
        return BusinessLocation::with('city')->where('business_id', $businessId)
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
            ->map(function (MenuItem $it) {
                $imageSrc = method_exists($it, 'getImageSrcAttribute') ? $it->image_src : ($it->image_url ?? null);

                return [
                    'id'           => $it->id,
                    'name'         => $it->name,
                    'description'  => $it->description,
                    'price'        => $it->price_cents,                         // view espera 'price'
                    'img_url'      => $it->image_url,                           // view espera 'img_url'
                    'prep_min'     => $it->prep_time_minutes,                   // view espera 'prep_min'
                    'tags'         => is_array($it->tags) ? implode(',', $it->tags) : ($it->tags ?? ''),
                    'is_available' => (bool) $it->is_available,
                    'sort_order'   => $it->sort_order,
                    'image_src'    => $imageSrc,
                ];
            })
            ->toArray();
    }

    public function createMenuItem(int $sectionId, array $data): int
    {
        if (empty($data['business_id'])) {
            throw new \InvalidArgumentException('business_id é obrigatório');
        }

        $payload = [
            'business_id'        => (int) $data['business_id'],
            'section_id'         => (int) $sectionId,
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'price_cents'        => (int) ($data['price'] ?? 0),               // price -> price_cents
            'image_url'          => $data['img_url'] ?? null,                  // img_url -> image_url
            'image_path'         => $data['image_path'] ?? null,               // se veio upload
            'is_available'       => array_key_exists('is_available',$data) ? (int) $data['is_available'] : 1,
            'sort_order'         => (int) ($data['sort_order'] ?? 0),
            'prep_time_minutes'  => (int) ($data['prep_min'] ?? 0),            // prep_min -> prep_time_minutes
            'tags'               => $data['tags'] ?? null,                     // se coluna for JSON, mantenha array
        ];

        $item = MenuItem::create($payload);
        return (int) $item->id;
    }

    public function updateMenuItem(int $itemId, array $data): bool
    {
        $it = MenuItem::findOrFail($itemId);

        $payload = [
            'name'               => $data['name'] ?? $it->name,
            'description'        => $data['description'] ?? $it->description,
            'price_cents'        => array_key_exists('price',$data) ? (int) $data['price'] : $it->price_cents,
            'image_url'          => array_key_exists('img_url',$data) ? $data['img_url'] : $it->image_url,
            'is_available'       => array_key_exists('is_available',$data) ? (int) $data['is_available'] : $it->is_available,
            'sort_order'         => array_key_exists('sort_order',$data) ? (int) $data['sort_order'] : $it->sort_order,
            'prep_time_minutes'  => array_key_exists('prep_min',$data) ? (int) $data['prep_min'] : $it->prep_time_minutes,
            'tags'               => array_key_exists('tags',$data) ? $data['tags'] : $it->tags,
        ];

        if (!empty($data['image_path'])) {
            $payload['image_path'] = $data['image_path'];
        }

        $it->fill($payload);
        return $it->save();
    }

    public function deleteMenuItem(int $itemId): bool {
        return (bool) MenuItem::where('id',$itemId)->delete();
    }

    public function findLocationOrFail(int $businessId, int $locationId)
    {
        return BusinessLocation::where('business_id', $businessId)
            ->findOrFail($locationId);
    }

    public function setLocationStatus(int $businessId, int $locationId, int $status): void
    {
        BusinessLocation::where('business_id', $businessId)
            ->where('id', $locationId)
            ->update(['status' => (int)$status]);
    }

}
