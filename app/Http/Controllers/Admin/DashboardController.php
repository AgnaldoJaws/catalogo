<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(Request $request, int $business)
    {
        $biz = $this->svc->findOrFail($business);

       //$this->authorize('view', $biz);

        return view('admin.dashboard', compact('biz'));
    }
}
