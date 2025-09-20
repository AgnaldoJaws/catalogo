<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LocationSearchServiceInterface
{
    /**
     * Busca de filiais para a Home, aplicando regras de negócio/transform.
     */
    public function search(array $filters, int $perPage = 12): LengthAwarePaginator;
}
