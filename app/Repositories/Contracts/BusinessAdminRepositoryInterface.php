<?php

namespace App\Repositories\Contracts;

use App\Models\Business;

interface BusinessAdminRepositoryInterface
{
    public function find(int $id): ?Business;
    public function updateProfile(int $id, array $data): bool;

    // Locations
    public function listLocations(int $businessId): array;
    public function upsertLocation(int $businessId, array $data): int; // retorna id
    public function deleteLocation(int $businessId, int $locationId): bool;

    // Menu
    public function listMenuSections(int $businessId): array;
    public function createMenuSection(int $businessId, array $data): int;
    public function updateMenuSection(int $sectionId, array $data): bool;
    public function deleteMenuSection(int $sectionId): bool;

    public function listMenuItems(int $sectionId): array;
    public function createMenuItem(int $sectionId, array $data): int;
    public function updateMenuItem(int $itemId, array $data): bool;
    public function deleteMenuItem(int $itemId): bool;
    public function findLocationOrFail(int $businessId, int $locationId);
    public function setLocationStatus(int $businessId, int $locationId, int $status): void;
    public function findWithRelations(int $id, array $relations = []): ?Business;
    public function syncCategories(int $businessId, array $categoryIds): void;

}
