<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Plan;
use App\Models\User;
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
        return view('home', ['plans' => $this->activePlans()]);
    }

    /** Public pricing page, built from the plans an admin has set up (same source as Plan & Billing). */
    public function pricing(): View
    {
        return view('pages.pricing', ['plans' => $this->activePlans()]);
    }

    /**
     * About page. Shows only platform-wide counts (accounts, entries, AI answers) —
     * never money totals, which would expose aggregate figures from other users' ledgers.
     */
    public function about(): View
    {
        $stats = Cache::store('file')->remember('about.platform_counts', now()->addMinutes(10), function () {
            return [
                'accounts' => User::count(),
                'entries'  => Income::count() + Expense::count(),
                'answers'  => AiChat::where('sender', AiChat::SENDER_AI)->count(),
            ];
        });

        return view('pages.about', ['stats' => $stats]);
    }

    /** Plans change rarely; caching avoids a round trip to the remote database on every visit. */
    private function activePlans()
    {
        return Cache::store('file')->remember('home.active_plans', now()->addMinutes(10), function () {
            return Plan::query()->where('is_active', true)->orderBy('sort_order')->get();
        });
    }
}
