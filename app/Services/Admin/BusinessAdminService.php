<?php

namespace App\Services\Admin;

use App\Models\Business;
use App\Repositories\Contracts\BusinessAdminRepositoryInterface;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessAdminService implements BusinessAdminServiceInterface
{
    public function __construct(private readonly BusinessAdminRepositoryInterface $repo) {}

    public function findOrFail(int $businessId): Business {
        $biz = $this->repo->find($businessId);
        if (!$biz) throw new ModelNotFoundException('Business not found');
        return $biz;
    }

    public function updateProfile(int $businessId, array $data): void {
        $this->repo->updateProfile($businessId, $data);
    }

    public function locations(int $businessId): array {
        return $this->repo->listLocations($businessId);
    }

    public function upsertLocation(int $businessId, array $data): int {
        return $this->repo->upsertLocation($businessId, $data);
    }

    public function deleteLocation(int $businessId, int $locationId): void {
        $this->repo->deleteLocation($businessId, $locationId);
    }

    public function sections(int $businessId): array {
        return $this->repo->listMenuSections($businessId);
    }

    public function createSection(int $businessId, array $data): int {
        return $this->repo->createMenuSection($businessId, $data);
    }

    public function updateSection(int $sectionId, array $data): void {
        $this->repo->updateMenuSection($sectionId, $data);
    }

    public function deleteSection(int $sectionId): void {
        $this->repo->deleteMenuSection($sectionId);
    }

    public function items(int $sectionId): array {
        return $this->repo->listMenuItems($sectionId);
    }

    public function createItem(int $sectionId, array $data): int {
        return $this->repo->createMenuItem($sectionId, $data);
    }

    public function updateItem(int $itemId, array $data): void {
        $this->repo->updateMenuItem($itemId, $data);
    }

    public function deleteItem(int $itemId): void {
        $this->repo->deleteMenuItem($itemId);
    }
}
