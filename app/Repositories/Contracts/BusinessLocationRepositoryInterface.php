<?php

namespace App\Repositories\Contracts;

use App\Models\BusinessLocation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BusinessLocationRepositoryInterface
{
    /**
     * Busca de filiais para a Home.
     * Filtros aceitos:
     *  - city_slug?: string
     *  - category_slug?: string
     *  - q?: string (texto livre, nome da marca/endereço/itens)
     *  - lat?: float, lng?: float, radius_km?: float
     *  - open_now?: bool
     *  - sort?: 'nearest'|'rating'|'az'|'za'|'items'
     */
    public function search(array $filters, int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): ?BusinessLocation;

    /** Lista todas as filiais de uma marca (por business_id) */
    public function listByBusiness(int $businessId): Collection;
}
