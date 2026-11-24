@extends('layouts.app')

@section('title', 'Reports · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format(abs((float) $v), 0);
    $signed = fn ($v) => ($v < 0 ? '−' : '') . '₹' . number_format(abs((float) $v), 0);
    $pct = fn ($now, $prev) => $prev > 0 ? round(($now - $prev) / $prev * 100) : null;
    $net = $totalIncome - $totalExpense;
    $prevNet = $prevIncome - $prevExpense;
    $rate = $totalIncome > 0 ? round($net / $totalIncome * 100, 1) : null;
    $monthsInRange = max(1, count($monthly));
    $expCatTotal = (float) $expenseCategories->sum('total');
    $incCatTotal = (float) $incomeCategories->sum('total');
    $palette = ['#6366f1', '#10b981', '#f59e0b', '#0ea5e9', '#f43f5e', '#a855f7', '#71717a'];
    $hasData = $totalIncome > 0 || $totalExpense > 0;
    $rangeText = $from->format('d M Y') . ' – ' . $to->format('d M Y');
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Reports" eyebrow="My money" icon="fa-wallet" :stamp="false"
        :description="'Your personal income and spending for ' . $rangeText . '.'">
        <x-slot name="actions">
            <a href="{{ route('user.expenses.export.pdf') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-file-pdf text-xs text-zinc-400" aria-hidden="true"></i> Expenses PDF
            </a>
            <a href="{{ route('user.incomes.export.pdf') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-file-pdf text-xs text-zinc-400" aria-hidden="true"></i> Income PDF
            </a>
        </x-slot>
    </x-admin.page-header>

    {{-- Period picker --}}
    <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-2 shadow-[0_1px_2px_rgba(24,24,27,.04)] lg:flex-row lg:items-center lg:justify-between">
        <nav class="flex gap-1 overflow-x-auto" aria-label="Report period">
            @foreach($presets as $key => $label)
                <a href="{{ route('user.reports.index', ['range' => $key]) }}" @if($preset === $key) aria-current="true" @endif
                   class="inline-flex h-8 shrink-0 items-center rounded-md px-3 text-sm font-medium {{ $preset === $key ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:bg-zinc-100' }}">{{ $label }}</a>
            @endforeach
        </nav>
        <form method="GET" action="{{ route('user.reports.index') }}" class="flex flex-wrap items-center gap-2 px-1">
            <label class="sr-only" for="from">From</label>
            <input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="h-8 rounded-md border border-zinc-200 px-2 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
            <span class="text-xs text-zinc-400">to</span>
            <label class="sr-only" for="to">To</label>
            <input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="h-8 rounded-md border border-zinc-200 px-2 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
            <button class="h-8 rounded-md {{ $preset === 'custom' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }} px-3 text-sm font-medium">Custom</button>
        </form>
    </div>

    {{-- AI: explain this period --}}
    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-white p-5 sm:p-6" aria-labelledby="ai-explain-title"
             x-data="{ state: 'idle', html: '', source: '',
                 run() {
                     this.state = 'loading';
                     fetch('{{ route('user.reports.explain', request()->only(['range', 'from', 'to'])) }}', { headers: { 'Accept': 'application/json' } })
                         .then(r => r.json()).then(j => { this.html = j.html; this.source = j.source; this.state = 'done'; })
                         .catch(() => { this.html = '<p>The explanation isn\'t available right now. Please try again.</p>'; this.source = 'error'; this.state = 'done'; });
                 } }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/30" aria-hidden="true"><i class="fa-solid fa-wand-magic-sparkles text-sm"></i></span>
                <div>
                    <h2 id="ai-explain-title" class="text-sm font-semibold text-zinc-900">Explain this period with AI</h2>
                    <p class="mt-0.5 text-sm text-zinc-500">What changed in {{ $rangeText }}, why it happened, and what to do next — written from the figures on this page.</p>
                </div>
            </div>
            <button type="button" @click="run()" :disabled="state === 'loading'"
                    class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-60 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">
                <i class="fa-solid" :class="state === 'loading' ? 'fa-circle-notch fa-spin' : 'fa-wand-magic-sparkles'" aria-hidden="true"></i>
                <span x-text="state === 'loading' ? 'Analysing…' : (state === 'done' ? 'Explain again' : 'Explain this period')">Explain this period</span>
            </button>
        </div>

        <div x-show="state === 'loading'" x-cloak class="mt-5 space-y-2 border-t border-indigo-100 pt-5" aria-label="Analysing">
            <div class="h-3 w-10/12 animate-pulse rounded bg-indigo-100"></div>
            <div class="h-3 w-8/12 animate-pulse rounded bg-indigo-100"></div>
            <div class="h-3 w-9/12 animate-pulse rounded bg-indigo-100"></div>
        </div>
        <div x-show="state === 'done'" x-cloak class="mt-5 border-t border-indigo-100 pt-5" aria-live="polite">
            <div class="ai-prose text-[15px] leading-relaxed text-zinc-700" x-html="html"></div>
            <p class="mt-4 text-xs text-zinc-400">
                <span x-show="source === 'gemini'"><i class="fa-solid fa-microchip mr-1" aria-hidden="true"></i>Written by Google Gemini from your own records. AI can make mistakes — check before acting on it.</span>
                <span x-show="source === 'rules'">Gemini was busy, so this was worked out by the app's built-in rules.</span>
            </p>
        </div>
    </section>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Income" :value="$inr($totalIncome)" icon="fa-arrow-trend-up" tone="emerald"
            :delta="$pct($totalIncome, $prevIncome)" deltaLabel="vs previous period" :spark="array_column($monthly, 'income')"
            :hint="$pct($totalIncome, $prevIncome) === null ? $incomeCount . ' ' . \Illuminate\Support\Str::plural('entry', $incomeCount) : null" />
        <x-admin.stat label="Expenses" :value="$inr($totalExpense)" icon="fa-arrow-trend-down" tone="rose"
            :delta="$pct($totalExpense, $prevExpense)" goodWhen="down" deltaLabel="vs previous period" :spark="array_column($monthly, 'expense')"
            :hint="$pct($totalExpense, $prevExpense) === null ? $expenseCount . ' ' . \Illuminate\Support\Str::plural('entry', $expenseCount) : null" />
        <x-admin.stat label="Net" :value="$signed($net)" icon="fa-scale-balanced" tone="indigo"
            :hint="$rate !== null ? $rate . '% of income kept' : 'No income in this period'" />
        <x-admin.stat label="Average monthly spend" :value="$inr($totalExpense / $monthsInRange)" icon="fa-calendar" tone="sky"
            :hint="'Over ' . $monthsInRange . ' ' . \Illuminate\Support\Str::plural('month', $monthsInRange)" />
    </div>

    @unless($hasData)
        <x-admin.card>
            <x-admin.empty icon="fa-chart-simple" title="No income or expenses in this period"
                text="Pick a longer period above, or add records to see your report.">
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('user.incomes.create') }}" class="inline-flex h-9 items-center rounded-lg border border-zinc-200 px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Add income</a>
                    <a href="{{ route('user.expenses.create') }}" class="inline-flex h-9 items-center rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800">Add expense</a>
                </div>
            </x-admin.empty>
        </x-admin.card>
    @else
        <x-admin.card title="Month by month" :description="$rangeText">
            <x-slot name="action">
                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Income</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Expenses</span>
                </div>
            </x-slot>
            <div class="h-64"><canvas id="reportChart" role="img" aria-label="Income and expenses per month in the selected period"></canvas></div>
        </x-admin.card>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach([['Spending by category', $expenseCategories, $expCatTotal, 'No expenses in this period'], ['Income by category', $incomeCategories, $incCatTotal, 'No income in this period']] as [$title, $rows, $sum, $emptyText])
                <x-admin.card :title="$title">
                    @if($rows->isEmpty())
                        <p class="text-sm text-zinc-500">{{ $emptyText }}.</p>
                    @else
                        <div class="flex h-2.5 overflow-hidden rounded-full bg-zinc-100">
                            @foreach($rows as $i => $c)<span style="width: {{ $sum > 0 ? $c->total / $sum * 100 : 0 }}%; background: {{ $palette[$i % 7] }}"></span>@endforeach
                        </div>
                        <table class="mt-4 w-full text-sm">
                            <thead class="sr-only"><tr><th>Category</th><th>Entries</th><th>Amount</th><th>Share</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($rows as $i => $c)
                                    <tr>
                                        <td class="py-2.5"><span class="flex items-center gap-2 text-zinc-700"><span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $palette[$i % 7] }}"></span>{{ $c->category }}</span></td>
                                        <td class="py-2.5 text-xs text-zinc-400">{{ $c->n }} {{ \Illuminate\Support\Str::plural('entry', $c->n) }}</td>
                                        <td class="py-2.5 text-right tabular-nums text-zinc-900">{{ $inr($c->total) }}</td>
                                        <td class="w-12 py-2.5 text-right text-xs tabular-nums text-zinc-400">{{ $sum > 0 ? round($c->total / $sum * 100) : 0 }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </x-admin.card>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach([['Largest expenses', $largestExpenses, 'title', 'expense_date', 'user.expenses.edit', false], ['Largest income', $largestIncome, 'source', 'income_date', 'user.incomes.edit', true]] as [$title, $rows, $tf, $df, $editRoute, $isIn])
                <x-admin.card :title="$title" flush>
                    @if($rows->isEmpty())
                        <p class="px-5 py-6 text-sm text-zinc-500">Nothing in this period.</p>
                    @else
                        <ol class="divide-y divide-zinc-100">
                            @foreach($rows as $i => $r)
                                <li>
                                    <a href="{{ route($editRoute, $r) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-zinc-50/70 sm:px-5">
                                        <span class="w-4 text-xs tabular-nums text-zinc-400">{{ $i + 1 }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-zinc-900">{{ $r->{$tf} }}</p>
                                            <p class="text-xs text-zinc-500">{{ $r->category }} · {{ \Carbon\Carbon::parse($r->{$df})->format('d M Y') }}</p>
                                        </div>
                                        <span class="text-sm font-semibold tabular-nums {{ $isIn ? 'text-emerald-600' : 'text-zinc-900' }}">{{ $inr($r->amount) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </x-admin.card>
            @endforeach
        </div>

        {{-- Comparison --}}
        <x-admin.card title="Compared with the previous period" :description="$prevFrom->format('d M Y') . ' – ' . $prevTo->format('d M Y')" flush>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-zinc-100 text-xs text-zinc-500">
                        <th class="px-5 py-2.5 text-left font-medium"></th><th class="px-4 py-2.5 text-right font-medium">Previous</th><th class="px-4 py-2.5 text-right font-medium">This period</th><th class="px-5 py-2.5 text-right font-medium">Change</th>
                    </tr></thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach([['Income', $prevIncome, $totalIncome, 'up'], ['Expenses', $prevExpense, $totalExpense, 'down'], ['Net', $prevNet, $net, 'up']] as [$label, $before, $now, $good])
                            @php $diff = $now - $before; $isGood = $diff == 0 ? null : (($diff > 0) === ($good === 'up')); @endphp
                            <tr>
                                <td class="px-5 py-3 font-medium text-zinc-800">{{ $label }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-zinc-500">{{ $signed($before) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-zinc-900">{{ $signed($now) }}</td>
                                <td class="px-5 py-3 text-right font-medium tabular-nums {{ $isGood === null ? 'text-zinc-400' : ($isGood ? 'text-emerald-600' : 'text-rose-600') }}">{{ $diff > 0 ? '+' : ($diff < 0 ? '−' : '') }}{{ $inr($diff) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endunless
</div>
@endsection

@push('styles')
<style>
    .ai-prose p { margin: 0 0 .75rem; } .ai-prose p:last-child { margin-bottom: 0; }
    .ai-prose ul, .ai-prose ol { margin: .25rem 0 .75rem 1.1rem; } .ai-prose ul { list-style: disc; } .ai-prose ol { list-style: decimal; }
    .ai-prose li { margin: .2rem 0; } .ai-prose strong { color: #18181b; font-weight: 600; }
</style>
@endpush

@if($hasData)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('reportChart');
    if (!el || typeof Chart === 'undefined') return;
    const inr = v => '₹' + Number(v).toLocaleString('en-IN');
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json(array_column($monthly, 'label')),
            datasets: [
                { label: 'Income', data: @json(array_column($monthly, 'income')), backgroundColor: '#10b981', borderRadius: 4, maxBarThickness: 26 },
                { label: 'Expenses', data: @json(array_column($monthly, 'expense')), backgroundColor: '#f43f5e', borderRadius: 4, maxBarThickness: 26 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.dataset.label + ': ' + inr(c.parsed.y) } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 11 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { color: '#a1a1aa', font: { size: 11 }, callback: v => v >= 1000 ? '₹' + (v / 1000) + 'k' : '₹' + v } },
            },
        },
    });
});
</script>
@endpush
@endif
