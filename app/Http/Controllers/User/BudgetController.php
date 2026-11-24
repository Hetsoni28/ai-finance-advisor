<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Services\Ai\AiInsightService;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Monthly category budgets with progress and AI alerts. */
class BudgetController extends Controller
{
    public function __construct(private BudgetService $budgets)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $rows = $this->budgets->overview(Auth::user());
        $set = $rows->whereNotNull('limit');

        return view('user.budgets.index', [
            'rows'          => $rows,
            'totalLimit'    => $set->sum('limit'),
            'totalSpent'    => $set->sum('spent'),
            'budgetedCount' => $set->count(),
            'daysLeft'      => $rows->first()['daysLeft'] ?? 0,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category'      => ['required', 'string', Rule::in(AiInsightService::EXPENSE_CATEGORIES)],
            'monthly_limit' => ['required', 'numeric', 'min:100', 'max:10000000'],
        ], [
            'monthly_limit.min' => 'Set a limit of at least ₹100.',
        ]);

        $existed = Budget::where('user_id', Auth::id())->where('category', $data['category'])->exists();
        $this->budgets->save(Auth::user(), $data['category'], round((float) $data['monthly_limit'], 2));

        return redirect()->route('user.budgets.index')
            ->with('success', ($existed ? 'Budget updated' : 'Budget set') . " for {$data['category']}.");
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === Auth::id(), 403);
        $category = $budget->category;
        $budget->delete();

        return redirect()->route('user.budgets.index')->with('success', "Budget for {$category} removed.");
    }
}
