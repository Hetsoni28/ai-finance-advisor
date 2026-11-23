<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public landing page. Only loads public data (active plans) — never ledger data,
     * because guests must not see aggregated totals from other users' accounts.
     */
    public function index(): View
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home', ['plans' => $plans]);
    }
}
