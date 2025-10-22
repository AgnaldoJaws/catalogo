<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaitlistRequest;
use App\Models\WaitlistLead;

class WaitlistController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

}
