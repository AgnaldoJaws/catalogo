<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(Request $request)
    {
        $business = Auth::user()->businesses->id;

        $biz = $this->svc->findOrFail($business);

       $this->authorize('view', $biz);

        return view('admin.dashboard', compact('biz'));
    }
}
