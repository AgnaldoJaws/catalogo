<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface MenuRepositoryInterface
{
    /**
     * Retorna cardápio estruturado (seções + itens) para exibição.
     * - Se existir menu por filial (business_loc_id), usa-o;
     * - Caso contrário, herda da marca (business_id).
     */
    public function getMenu(string $businessSlug, ?int $locationId = null): array;
}
