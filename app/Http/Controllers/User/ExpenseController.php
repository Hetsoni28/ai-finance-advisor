<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Throwable;

class ExpenseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX & ANALYTICS DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403, 'Cryptographic handshake failed.');

        // 1. Base Query (Personal Expenses Only)
        $baseQuery = $user->expenses()
            ->where('is_personal', true)
            ->whereNull('family_id');

        // 2. Apply Smart Filters
        if ($request->filled('search')) {
            $search = trim($request->search);
            $baseQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', ltrim($search, '#EXP-'));
            });
        }

        if ($request->filled('category')) {
            $baseQuery->where('category', $request->category);
        }

        if ($request->filled('from')) {
            $baseQuery->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $baseQuery->whereDate('expense_date', '<=', $request->to);
        }

        // 3. Global Analytics (Calculated via SQL, independent of pagination)
        $total = (float) (clone $baseQuery)->sum('amount');
        
        $topCategory = (clone $baseQuery)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->value('category');

        $latest = (clone $baseQuery)->latest('updated_at')->value('updated_at');

        // 4. Paginated Ledger for the Table
        $expenses = (clone $baseQuery)
            ->latest('expense_date')
            ->latest('id') // Tie-breaker for identical dates
            ->paginate(10)
            ->withQueryString();

        return view('user.expenses.index', compact(
            'expenses',
            'total',
            'topCategory',
            'latest'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        return view('user.expenses.create', [
            'families' => $user->families()->get(),
            'recentExpenses' => $user->expenses()
                ->where('is_personal', true)
                ->whereNull('family_id')
                ->latest('expense_date')
                ->limit(5)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (FIXED: Added robust try-catch block around transaction)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'family_id'    => 'nullable|exists:families,id',
            'title'        => 'required|string|max:150',
            'category'     => 'required|string|max:50',
            'amount'       => 'required|numeric|min:0.01|max:999999999',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {

                $familyId = $validated['family_id'] ?? null;

                // Verify User Belongs to Family
                if ($familyId) {
                    abort_unless(
                        $user->families()->where('families.id', $familyId)->exists(),
                        403,
                        'Unauthorized family access.'
                    );
                }

                $isPersonal = $familyId ? false : true;

                $expense = Expense::create([
                    'user_id'      => $user->id,
                    'family_id'    => $familyId,
                    'is_personal'  => $isPersonal,
                    'title'        => trim($validated['title']),
                    'category'     => $validated['category'],
                    'amount'       => (float) $validated['amount'],
                    'expense_date' => $validated['expense_date'],
                ]);

                Activity::create([
                    'user_id'     => $user->id,
                    'description' => ($isPersonal ? 'Personal' : 'Family') . 
                                     ' expense recorded: ' . $expense->title . 
                                     ' (₹' . number_format($expense->amount, 2) . ')',
                ]);
            });

            if (!empty($validated['family_id'])) {
                return redirect()->route('user.families.show', $validated['family_id'])
                                 ->with('success', 'Family expense added successfully.');
            }

            return redirect()->route('user.expenses.index')
                             ->with('success', 'Transaction securely recorded.');

        } catch (Throwable $e) {
            Log::error('Expense Creation Failed: ' . $e->getMessage(), ['user_id' => $user->id, 'payload' => $validated]);
            return back()->withInput()->with('error', 'Database synchronization failed. Please review your payload and try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Expense $expense): View
    {
        abort_unless($expense->user_id === auth()->id(), 403);

        return view('user.expenses.edit', compact('expense'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless($expense->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title'        => 'required|string|max:150',
            'category'     => 'required|string|max:50',
            'amount'       => 'required|numeric|min:0.01|max:999999999',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            DB::transaction(function () use ($expense, $validated) {
                $expense->update([
                    'title'        => trim($validated['title']),
                    'category'     => $validated['category'],
                    'amount'       => (float) $validated['amount'],
                    'expense_date' => $validated['expense_date'],
                ]);

                Activity::create([
                    'user_id'     => auth()->id(),
                    'description' => 'Modified transaction details: ' . $expense->title,
                ]);
            });

            return redirect()->route('user.expenses.index')->with('success', 'Transaction ledger updated successfully.');

        } catch (Throwable $e) {
            Log::error("Expense Update Error: " . $e->getMessage(), ['expense_id' => $expense->id]);
            return back()->withInput()->with('error', 'Failed to update transaction. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Expense $expense): RedirectResponse
    {
        abort_unless($expense->user_id === auth()->id(), 403);

        try {
            DB::transaction(function () use ($expense) {
                Activity::create([
                    'user_id'     => auth()->id(),
                    'description' => 'Archived/Deleted transaction: ' . $expense->title,
                ]);

                $expense->delete();
            });

            return redirect()->back()->with('success', 'Transaction successfully purged from ledger.');

        } catch (Throwable $e) {
            Log::error("Expense Deletion Error: " . $e->getMessage(), ['expense_id' => $expense->id]);
            return back()->with('error', 'Failed to purge transaction due to a system lock.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf()
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        try {
            $baseQuery = $user->expenses()
                ->where('is_personal', true)
                ->whereNull('family_id');

            // Calculate totals using DB raw instead of pulling everything into RAM
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

            // Fetch the top 100 recent for the PDF log to prevent 10,000 page PDFs
            $expenses = (clone $baseQuery)->latest('expense_date')->limit(100)->get();

            $reportId = 'FA-EXP-' . now()->format('YmdHis');

            $pdf = Pdf::loadView(
                'user.expenses.pdf',
                compact('expenses', 'summary', 'reportId')
            )->setPaper('a4', 'portrait');

            return $pdf->download('FinanceAI_Personal_Ledger_' . now()->format('d-m-Y') . '.pdf');

        } catch (Throwable $e) {
            Log::error('Expense PDF Export Error: ' . $e->getMessage(), ['user_id' => $user->id]);
            return back()->with('error', 'Failed to generate PDF document. The rendering engine may be busy.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV (FIXED: Memory optimization using toBase())
    |--------------------------------------------------------------------------
    */
    public function exportCsv(): StreamedResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $fileName = 'FinanceAI_Ledger_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Transaction ID', 'Title / Merchant', 'Category', 'Amount (INR)', 'Date', 'Type'];

        $callback = function () use ($user, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // 🚨 BEAST MODE FIX: ->toBase() fetches raw arrays instead of hydrating heavy Eloquent Models
            // This prevents PHP Out-Of-Memory crashes if the user has 100,000+ transactions.
            $user->expenses()
                ->where('is_personal', true)
                ->whereNull('family_id')
                ->orderBy('expense_date', 'desc')
                ->toBase() 
                ->chunk(1000, function ($expenses) use ($file) {
                    foreach ($expenses as $exp) {
                        fputcsv($file, [
                            'EXP-' . str_pad((string)$exp->id, 5, '0', STR_PAD_LEFT),
                            $exp->title,
                            $exp->category ?? 'General',
                            $exp->amount,
                            Carbon::parse($exp->expense_date ?? $exp->created_at)->format('Y-m-d'),
                            'Outflow'
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}