<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Carbon;
use Throwable;

class ReportController extends Controller
{
    /**
     * Enforce strict authentication and administrative privileges.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN REPORT & ANALYTICS DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $cacheKey = "admin_report_dashboard_v6_page_{$page}";

        $data = Cache::remember($cacheKey, 60, function () {

            // 1. Core Platform Metrics
            $totalUsers    = User::count();
            $activeUsers   = User::where('created_at', '>=', now()->subDays(30))->count();
            $totalIncome   = (float) Income::sum('amount');
            $totalExpenses = (float) Expense::sum('amount');

            // 2. Security Audit Feed
            $activities = Activity::with('causer')->latest()->paginate(10);

            // 🚨 FIX: Laravel's native `through` method is the cleanest way to map paginators (Fixes IDE Error)
            $activities->through(function ($activity) {
                $activity->setAttribute('user', $activity->causer);
                return $activity;
            });

            // 3. Time-Series Financial Data
            [$labels, $monthlyIncome, $monthlyExpenses] = $this->getMonthlyData();

            // 4. Categorical Distribution Data
            [$categoryLabels, $categorySeries] = $this->getCategoryData();

            // 5. Top Spenders Aggregation
            $topSpenders = Expense::selectRaw("user_id, SUM(amount) as total")
                ->with('user:id,name,email')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            return compact(
                'totalUsers', 'activeUsers', 'totalIncome', 'totalExpenses',
                'activities', 'monthlyIncome', 'monthlyExpenses', 'labels',
                'categoryLabels', 'categorySeries', 'topSpenders'
            );
        });

        return view('admin.reports.index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | PDF GENERATION ENGINE
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
    {
        try {
            $now = now();

            // People
            $users = User::query()
                ->withCount(['incomes', 'expenses'])
                ->with(['activeSubscription.plan:id,name'])
                ->orderByDesc('created_at')
                ->limit(300)
                ->get(['id', 'name', 'email', 'role', 'is_admin', 'is_blocked', 'created_at']);

            $people = [
                'total'     => User::count(),
                'suspended' => User::where('is_blocked', true)->count(),
                'admins'    => User::where(fn ($q) => $q->where('role', 'admin')->orWhere('is_admin', true))->count(),
                'new30'     => User::where('created_at', '>=', $now->copy()->subDays(30))->count(),
            ];
            $people['active'] = $people['total'] - $people['suspended'];

            // Money recorded by all users
            $income   = (float) Income::sum('amount');
            $expenses = (float) Expense::sum('amount');
            $money = [
                'income'      => $income,
                'expenses'    => $expenses,
                'net'         => $income - $expenses,
                'rate'        => $income > 0 ? ($income - $expenses) / $income * 100 : 0,
                'incomeRows'  => Income::count(),
                'expenseRows' => Expense::count(),
            ];

            // Last six months, by transaction date
            $months = collect(range(5, 0))->map(function ($ago) use ($now) {
                $d = $now->copy()->startOfMonth()->subMonthsNoOverflow($ago);
                $inc = (float) Income::whereYear('income_date', $d->year)->whereMonth('income_date', $d->month)->sum('amount');
                $exp = (float) Expense::whereYear('expense_date', $d->year)->whereMonth('expense_date', $d->month)->sum('amount');
                return (object) [
                    'label'    => $d->format('F Y'),
                    'income'   => $inc,
                    'expenses' => $exp,
                    'net'      => $inc - $exp,
                    'signups'  => User::whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count(),
                ];
            });

            $categories = Expense::selectRaw('category, COUNT(*) as entries, SUM(amount) as total')
                ->groupBy('category')->orderByDesc('total')->get();

            // Plans and subscriptions
            $plans = \App\Models\Plan::orderBy('sort_order')->get(['id', 'name', 'price'])->map(function ($p) use ($now) {
                $active = \App\Models\UserSubscription::where('plan_id', $p->id)->where('status', 'active')
                    ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>', $now));
                return (object) [
                    'name'    => $p->name,
                    'price'   => (float) $p->price,
                    'active'  => (clone $active)->distinct('user_id')->count('user_id'),
                    'revenue' => (float) \App\Models\UserSubscription::where('plan_id', $p->id)->sum('amount_paid'),
                    'sold'    => \App\Models\UserSubscription::where('plan_id', $p->id)->count(),
                ];
            });
            $paidUsers = \App\Models\UserSubscription::where('status', 'active')
                ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>', $now))
                ->whereHas('plan', fn ($q) => $q->where('price', '>', 0))
                ->distinct('user_id')->count('user_id');

            $activity = \App\Models\Activity::with('user:id,name')->latest()->limit(15)->get();
            $messages = [
                'total'  => \App\Models\Contact::count(),
                'unread' => \App\Models\Contact::where('is_read', false)->count(),
            ];

            $pdf = Pdf::loadView('pdf.admin-report', [
                'people'      => $people,
                'paidUsers'   => $paidUsers,
                'money'       => $money,
                'months'      => $months,
                'categories'  => $categories,
                'plans'       => $plans,
                'users'       => $users,
                'activity'    => $activity,
                'messages'    => $messages,
                'admin'       => auth()->user(),
                'reportNo'    => 'ADM-' . $now->format('ymd-His'),
                'generatedAt' => $now->format('d M Y, h:i A'),
            ])->setPaper('a4', 'portrait');

            return $pdf->download('FinanceAI_Platform_Report_' . $now->format('Y-m-d') . '.pdf');

        } catch (Throwable $e) {
            Log::error('Admin PDF export failed: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return back()->with('error', 'We couldn’t create the PDF report. Please try again in a moment.');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CSV RAW DATA EXPORT
    |--------------------------------------------------------------------------
    */

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'FinanceAI_Raw_Ledger_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Transaction ID', 'User ID', 'Type', 'Category', 'Amount (INR)', 'Date', 'Description'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $incCol = \Illuminate\Support\Facades\Schema::hasColumn('incomes', 'income_date') ? 'income_date' : 'created_at';
            $expCol = \Illuminate\Support\Facades\Schema::hasColumn('expenses', 'expense_date') ? 'expense_date' : 'created_at';

            Income::orderBy('id')->chunk(1000, function ($incomes) use ($file, $incCol) {
                foreach ($incomes as $inc) {
                    fputcsv($file, [
                        'INC-' . $inc->id, $inc->user_id, 'Inflow', $inc->category ?? 'General Income',
                        $inc->amount, Carbon::parse($inc->$incCol)->format('Y-m-d'), $inc->source ?? ''
                    ]);
                }
            });

            Expense::orderBy('id')->chunk(1000, function ($expenses) use ($file, $expCol) {
                foreach ($expenses as $exp) {
                    fputcsv($file, [
                        'EXP-' . $exp->id, $exp->user_id, 'Outflow', $exp->category ?? 'Uncategorized',
                        $exp->amount, Carbon::parse($exp->$expCol)->format('Y-m-d'), $exp->title ?? ''
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL DATA AGGREGATION HELPERS
    |--------------------------------------------------------------------------
    */

    private function getMonthlyData(): array
    {
        $labels = collect();
        $incomeSeries = collect();
        $expenseSeries = collect();

        // Two grouped queries instead of twelve per-month sums.
        $from = now()->subMonthsNoOverflow(5)->startOfMonth();
        $incomeByMonth = Income::where('income_date', '>=', $from)
            ->selectRaw("DATE_FORMAT(income_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')->pluck('total', 'ym');
        $expenseByMonth = Expense::where('expense_date', '>=', $from)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')->pluck('total', 'ym');

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonthsNoOverflow($i);
            $labels->push($month->format('M'));
            $incomeSeries->push((float) ($incomeByMonth[$month->format('Y-m')] ?? 0));
            $expenseSeries->push((float) ($expenseByMonth[$month->format('Y-m')] ?? 0));
        }

        return [$labels->toArray(), $incomeSeries->toArray(), $expenseSeries->toArray()];
    }

    private function getCategoryData(): array
    {
        try {
            $hasCategoryColumn = \Illuminate\Support\Facades\Schema::hasColumn('expenses', 'category');
            
            if (!$hasCategoryColumn) {
                return [[], []];
            }

            $catRaw = Expense::selectRaw("category, SUM(amount) as total")
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            if ($catRaw->isEmpty()) {
                return [[], []];
            }

            $labels = $catRaw->pluck('category')->toArray();
            $series = $catRaw->pluck('total')->map(fn($val) => (float)$val)->toArray();

            return [$labels, $series];

        } catch (Throwable $e) {
            Log::warning("Category Extraction Failed: " . $e->getMessage());
            return [[], []];
        }
    }
}