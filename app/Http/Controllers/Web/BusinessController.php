<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\{
    BusinessServiceInterface,
    MenuServiceInterface
};

class BusinessController extends Controller
{
    public function __construct(
        private readonly BusinessServiceInterface $biz,
        private readonly MenuServiceInterface $menu
    ) {}

    public function show(string $slug)
    {
        $business = $this->biz->showBySlug($slug);
        if (!$business) {
            abort(404);
        }

        $menu = $this->menu->getMenu($slug);

        return view('business.show', [
            'business' => $business,
            'sections' => $menu['sections'] ?? [],
        ]);
    }
}
