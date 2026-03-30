<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class IncomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX: Master Ledger View
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403, 'Cryptographic handshake failed.');

        // Initialize base query with personal scope
        $baseQuery = $user->incomes()->personal();

        // Dynamic Search Engine
        if ($request->filled('search')) {
            $search = trim($request->search);
            $baseQuery->where(function($q) use ($search) {
                $q->where('source', 'like', "%{$search}%")
                  ->orWhere('id', ltrim($search, '#INC-'));
            });
        }

        // Categorical Filtering
        if ($request->filled('category')) {
            $baseQuery->where('category', $request->category);
        }

        // Temporal Filtering
        if ($request->filled('from')) {
            $baseQuery->whereDate('income_date', '>=', $request->from);
        }

        // Telemetry Statistics Aggregation
        $statsData = (clone $baseQuery)->selectRaw('
            COUNT(*) as count,
            SUM(amount) as total,
            SUM(CASE WHEN MONTH(income_date) = ? AND YEAR(income_date) = ? THEN amount ELSE 0 END) as currentMonth
        ', [now()->month, now()->year])->first();

        $total = (float) ($statsData->total ?? 0);
        $currentMonth = (float) ($statsData->currentMonth ?? 0);
        $count = (int) ($statsData->count ?? 0);
        $average = $count > 0 ? round($total / $count, 2) : 0.0;

        // Paginated Collection
        $incomes = (clone $baseQuery)
            ->latest('income_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('user.income.index', [
            'incomes' => $incomes,
            'stats' => [
                'total'        => $total,
                'currentMonth' => $currentMonth,
                'average'      => $average,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE: Inbound Capital Form
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        return view('user.income.create', [
            'families' => $user->families()->get(),
            'recentIncome' => $user->incomes()
                ->personal()
                ->latest('income_date')
                ->take(5)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE: Transaction Persistence
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        // Strict Payload Validation
        $data = $request->validate([
            'income_type' => ['required', 'in:personal,family'],
            'family_id'   => ['nullable', 'exists:families,id'],
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'source'      => ['required', 'string', 'max:150'],
            'category'    => ['required', 'string', 'max:50'], 
            'income_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        try {
            DB::transaction(function () use ($user, $data) {
                $isPersonal = $data['income_type'] === 'personal';
                $familyId = null;

                if (!$isPersonal) {
                    abort_unless(
                        !empty($data['family_id']) &&
                        $user->families()->where('families.id', $data['family_id'])->exists(),
                        403,
                        'Unauthorized family hub access.'
                    );
                    $familyId = $data['family_id'];
                }

                // 1. Persist Record
                $income = Income::create([
                    'user_id'     => $user->id,
                    'family_id'   => $familyId,
                    'is_personal' => $isPersonal,
                    'amount'      => (float) $data['amount'],
                    'source'      => trim($data['source']),
                    'category'    => trim($data['category']),
                    'income_date' => Carbon::parse($data['income_date']),
                ]);

                // 2. Append Audit Trail
                Activity::create([
                    'user_id'     => $user->id,
                    'description' => ($isPersonal ? 'Personal' : 'Family') . 
                                     " income added: {$income->source} (+₹" . 
                                     number_format($income->amount, 2) . ")",
                ]);
            });

            return redirect()
                ->route('user.incomes.index')
                ->with('success', 'Inbound capital successfully recorded to the ledger.');

        } catch (Throwable $e) {
            Log::error('Income Creation Failed: ' . $e->getMessage(), ['user_id' => $user->id, 'payload' => $data]);
            return back()->withInput()->with('error', 'Database synchronization failed. Please review your payload and try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW: Record Details
    |--------------------------------------------------------------------------
    */
    public function show(Income $income): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403, 'Permission Denied.');

        return view('user.income.show', compact('income'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT: Modification Form
    |--------------------------------------------------------------------------
    */
    public function edit(Income $income): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403, 'Permission Denied.');

        return view('user.income.edit', compact('income'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE: Record Modification
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Income $income): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403);

        $data = $request->validate([
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'source'      => ['required', 'string', 'max:150'],
            'category'    => ['required', 'string', 'max:50'],
            'income_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        try {
            DB::transaction(function () use ($income, $data, $user) {
                // 1. Update Record
                $income->update([
                    'amount'      => (float) $data['amount'],
                    'source'      => trim($data['source']),
                    'category'    => trim($data['category']), 
                    'income_date' => Carbon::parse($data['income_date']),
                ]);

                // 2. Append Audit Trail
                Activity::create([
                    'user_id'     => $user->id,
                    'description' => "Income record modified: {$income->source} (+₹" . 
                                     number_format($income->amount, 2) . ")",
                ]);
            });

            return redirect()
                ->route('user.incomes.index')
                ->with('success', 'Cryptographic record updated successfully.');

        } catch (Throwable $e) {
            Log::error('Income Update Failed: ' . $e->getMessage(), ['income_id' => $income->id, 'user_id' => $user->id]);
            return back()->withInput()->with('error', 'Update protocol failed. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY: Record Voiding
    |--------------------------------------------------------------------------
    */
    public function destroy(Income $income): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403);

        try {
            DB::transaction(function () use ($income, $user) {
                // 1. Append Audit Trail Before Deletion
                Activity::create([
                    'user_id'     => $user->id,
                    'description' => 'Income record permanently voided: ' . $income->source,
                ]);

                // 2. Delete Record
                $income->delete();
            });

            return redirect()
                ->route('user.incomes.index')
                ->with('success', 'Income record permanently removed from the ledger.');

        } catch (Throwable $e) {
            Log::error('Income Deletion Failed: ' . $e->getMessage(), ['income_id' => $income->id]);
            return back()->with('error', 'Failed to void the record due to a system lock.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT: PDF Generation
    |--------------------------------------------------------------------------
    */
    public function exportPdf()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        try {
            $baseQuery = $user->incomes()->personal();

            $stats = (clone $baseQuery)->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total,
                AVG(amount) as average,
                MAX(amount) as highest
            ')->first();

            $topCategory = (clone $baseQuery)->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->value('category');

            $summary = [
                'total'       => (float) ($stats->total ?? 0),
                'count'       => (int) ($stats->count ?? 0),
                'average'     => (float) ($stats->average ?? 0),
                'highest'     => (float) ($stats->highest ?? 0),
                'topCategory' => $topCategory ?? 'Uncategorized',
            ];

            // Limit to top 100 for PDF performance
            $incomes = (clone $baseQuery)->latest('income_date')->limit(100)->get();
            $reportId = 'FA-INC-' . now()->format('YmdHis');

            $pdf = Pdf::loadView(
                'user.income.pdf', 
                compact('incomes', 'summary', 'reportId')
            )->setPaper('a4', 'portrait');

            return $pdf->download('FinanceAI_Income_Ledger_' . now()->format('Ymd') . '.pdf');

        } catch (Throwable $e) {
            Log::error('Income PDF Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF document. The rendering engine may be busy.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT: CSV Stream (Memory Optimized)
    |--------------------------------------------------------------------------
    */
    public function exportCsv(): StreamedResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $fileName = 'FinanceAI_Income_Ledger_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Transaction ID', 'Income Source', 'Category', 'Amount (INR)', 'Date', 'Type'];

        $callback = function () use ($user, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // 🚨 BEAST MODE FIX: ->toBase() fetches raw arrays instead of hydrating heavy Eloquent Models
            // This prevents PHP Out-Of-Memory crashes if the user has 100,000+ transactions.
            $user->incomes()
                ->personal()
                ->orderBy('income_date', 'desc')
                ->toBase() 
                ->chunk(1000, function ($incomes) use ($file) {
                    foreach ($incomes as $inc) {
                        fputcsv($file, [
                            'INC-' . str_pad((string)$inc->id, 5, '0', STR_PAD_LEFT),
                            $inc->source,
                            $inc->category ?? 'General',
                            $inc->amount,
                            Carbon::parse($inc->income_date ?? $inc->created_at)->format('Y-m-d'),
                            'Inflow'
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}