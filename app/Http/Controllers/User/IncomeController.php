<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class IncomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $query = $user->incomes()->personal();

        $incomes = (clone $query)
            ->latest('income_date')
            ->paginate(10);

        $total = (float) (clone $query)->sum('amount');

        $currentMonth = (float) (clone $query)
            ->whereMonth('income_date', now()->month)
            ->whereYear('income_date', now()->year)
            ->sum('amount');

        $count = (int) (clone $query)->count();

        $average = $count > 0
            ? round($total / $count, 2)
            : 0.0;

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
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        /** @var User $user */
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
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $data = request()->validate([
            'income_type' => ['required', 'in:personal,family'],
            'family_id'   => ['nullable', 'exists:families,id'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'source'      => ['required', 'string', 'max:255'],
            'income_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($user, $data) {

            $isPersonal = $data['income_type'] === 'personal';
            $familyId = null;

            if (!$isPersonal) {

                abort_unless(
                    !empty($data['family_id']) &&
                    $user->families()
                        ->where('families.id', $data['family_id'])
                        ->exists(),
                    403
                );

                $familyId = $data['family_id'];
            }

            $income = Income::create([
                'user_id'     => $user->id,
                'family_id'   => $familyId,
                'is_personal' => $isPersonal,
                'amount'      => (float) $data['amount'],
                'source'      => trim($data['source']),
                'income_date' => Carbon::parse($data['income_date']),
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'description' =>
                    ($isPersonal ? 'Personal' : 'Family') .
                    " income added: {$income->source} (₹" .
                    number_format($income->amount, 2) . ")",
            ]);
        });

        return redirect()
            ->route('user.incomes.index')
            ->with('success', 'Income added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Income $income): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403);

        return view('user.income.edit', [
            'income' => $income,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Income $income): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403);

        $data = request()->validate([
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'source'      => ['required', 'string', 'max:255'],
            'income_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($income, $data, $user) {

            $income->update([
                'amount'      => (float) $data['amount'],
                'source'      => trim($data['source']),
                'income_date' => Carbon::parse($data['income_date']),
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'description' =>
                    "Income updated: {$income->source} (₹" .
                    number_format($income->amount, 2) . ")",
            ]);
        });

        return redirect()
            ->route('user.incomes.index')
            ->with('success', 'Income updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Income $income): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($income->user_id === $user->id, 403);

        DB::transaction(function () use ($income, $user) {

            Activity::create([
                'user_id'     => $user->id,
                'description' => 'Income deleted: ' . $income->source,
            ]);

            $income->delete();
        });

        return redirect()
            ->route('user.incomes.index')
            ->with('success', 'Income deleted successfully.');
    }
}