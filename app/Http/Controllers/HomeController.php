<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public landing page. Only loads public data (active plans) — never ledger data,
     * because guests must not see aggregated totals from other users' accounts.
     */
    public function index(): View
    {
        // Plans change rarely; caching avoids a round trip to the remote database on every visit.
        $plans = Cache::store('file')->remember('home.active_plans', now()->addMinutes(10), function () {
            return Plan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return view('home', ['plans' => $plans]);
    }
}
