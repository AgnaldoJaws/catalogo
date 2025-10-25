<?php

namespace App\Services\Admin;

use App\Models\Business;
use App\Repositories\Contracts\BusinessAdminRepositoryInterface;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psy\Util\Str;

class BusinessAdminService implements BusinessAdminServiceInterface
{
    public function __construct(private readonly BusinessAdminRepositoryInterface $repo) {}

    public function findOrFail(int $businessId): Business {
        $biz = $this->repo->find($businessId);
        if (!$biz) throw new ModelNotFoundException('Business not found');
        return $biz;
    }

    public function updateProfile(int $businessId, array $data): void {
        if (!empty($data['name'])) {

            $data['slug'] = $this->slugify($data['name']);
        }
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

    public function findLocationOrFail(int $businessId, int $locationId)
    {
        return $this->repo->findLocationOrFail($businessId, $locationId);
    }

    public function setLocationStatus(int $businessId, int $locationId, int $status): void
    {
        $this->repo->setLocationStatus($businessId, $locationId, $status);
    }

    private function slugify(string $text): string
    {
        if (function_exists('iconv')) {
            $original = $text;
            $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($text === false) $text = $original;
        }

        $map = [
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','Á'=>'A','À'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','Í'=>'I','Ì'=>'I','Î'=>'I','Ï'=>'I',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','Ó'=>'O','Ò'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U',
            'ñ'=>'n','Ñ'=>'N','ç'=>'c','Ç'=>'C',
        ];
        $text = strtr($text, $map);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }

}
