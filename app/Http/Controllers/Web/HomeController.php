<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use App\Services\Contracts\{
    DirectoryServiceInterface,
    LocationSearchServiceInterface
};

class HomeController extends Controller
{
    public function __construct(
        private readonly DirectoryServiceInterface $directory,
        private readonly LocationSearchServiceInterface $search
    ) {}

    public function index(Request $request)
    {

        // filtros vindos do front
        $filters = $request->only([
            'city_slug','category_slug','q','lat','lng','radius_km','open_now','sort'
        ]);

        $perPage = (int) $request->get('per_page', 12);

        $page = $this->search->search($filters, $perPage);

        return view('home.index', [
            'filters'     => $filters,
            'cities'      => $this->directory->listCities(),
            'categories'  => $this->directory->listCategories(),
            'page'        => $page,                     // LengthAwarePaginator
        ]);
    }
}
