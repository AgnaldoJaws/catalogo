<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(int $business)
    {
        $biz = $this->svc->findOrFail($business);
        //$this->authorize('manage', $biz);

        $locations = $this->svc->locations($business);
        return view('admin.locations.index', compact('biz','locations'));
    }

    public function store(Request $request, int $business)
    {
        $biz = $this->svc->findOrFail($business);
        //$this->authorize('manage', $biz);

        $data = $request->validate([
            'id'      => 'nullable|integer',
            'city_id' => 'required|integer',
            'address' => 'required|string|max:255',
            'lat'     => 'nullable|numeric',
            'lng'     => 'nullable|numeric',
            'status'  => 'required|integer',
            'phone'   => 'nullable|string|max:30',
            'whatsapp'=> 'nullable|string|max:30',
        ]);

        $this->svc->upsertLocation($business, $data);
        return back()->with('ok','Local salvo!');
    }

    public function destroy(int $business, int $location)
    {
        $biz = $this->svc->findOrFail($business);
        //$this->authorize('manage', $biz);

        $this->svc->deleteLocation($business, $location);
        return back()->with('ok','Local removido!');
    }
}
