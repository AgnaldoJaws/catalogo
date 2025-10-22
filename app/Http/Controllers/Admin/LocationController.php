<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index()
    {
        $cities = City::orderBy('name')->get();
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $locations = $this->svc->locations($business);

        return view('admin.locations.index', compact('biz','locations','cities'));
    }

    public function store(Request $request)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

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

    public function destroy(int $location)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $this->svc->deleteLocation($business, $location);
        return back()->with('ok','Local removido!');
    }

    public function show(int $location)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $loc = $this->svc->findLocationOrFail($business, $location);

        return response()->json([
            'id'       => $loc->id,
            'city_id'  => $loc->city_id,
            'address'  => $loc->address,
            'lat'      => $loc->lat,
            'lng'      => $loc->lng,
            'phone'    => $loc->phone,
            'whatsapp' => $loc->whatsapp,
            'status'   => (int)$loc->status,
        ]);
    }

    public function status(Request $request, int $location)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $data = $request->validate(['status' => 'required|in:0,1']);
        $this->svc->setLocationStatus($business, $location, (int) $data['status']);

        return back()->with('ok', $data['status'] ? 'Loja aberta!' : 'Loja fechada!');
    }
}
