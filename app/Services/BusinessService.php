<?php

namespace App\Services;

use App\Repositories\Contracts\BusinessLocationRepositoryInterface;
use App\Repositories\Contracts\BusinessRepositoryInterface;
use App\Services\Contracts\BusinessServiceInterface;

class BusinessService implements BusinessServiceInterface
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businesses,
        private readonly BusinessLocationRepositoryInterface $locations
    ) {}

    public function showBySlug(string $slug): ?array
    {
        $b = $this->businesses->findBySlug($slug);
        if (!$b) return null;

        $locs = $this->locations->listByBusiness($b->id);

        return [
            'id'         => $b->id,
            'name'       => $b->name,
            'slug'       => $b->slug,
            'logo_url'   => $b->logo_url,
            'about'      => $b->about,
            'avg_rating' => (float) $b->avg_rating,
            'items_count'=> (int) $b->items_count,
            'categories' => $b->categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'slug'=>$c->slug])->values(),
            'locations'  => $locs->map(fn($l)=>[
                'id'       => $l->id,
                'city'     => $l->city?->name,
                'address'  => $l->address,
                'lat'      => $l->lat,
                'lng'      => $l->lng,
                'whatsapp' => $l->whatsapp,
                'phone'    => $l->phone,
                'status'   => $l->status,
            ])->values(),
        ];
    }
}
