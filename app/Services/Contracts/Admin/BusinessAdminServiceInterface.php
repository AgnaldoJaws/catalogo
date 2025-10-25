<?php

namespace App\Services\Contracts\Admin;

use App\Models\Business;

interface BusinessAdminServiceInterface
{
    public function findOrFail(int $businessId): Business;
    public function updateProfile(int $businessId, array $data): void;

    public function locations(int $businessId): array;
    public function upsertLocation(int $businessId, array $data): int;
    public function deleteLocation(int $businessId, int $locationId): void;

    public function sections(int $businessId): array;
    public function createSection(int $businessId, array $data): int;
    public function updateSection(int $sectionId, array $data): void;
    public function deleteSection(int $sectionId): void;

    public function items(int $sectionId): array;
    public function createItem(int $sectionId, array $data): int;
    public function updateItem(int $itemId, array $data): void;
    public function deleteItem(int $itemId): void;

    public function findLocationOrFail(int $businessId, int $locationId);
    public function setLocationStatus(int $businessId, int $locationId, int $status): void;
}
