@extends('layouts.app')

@section('title', 'Dashboard · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format(abs((float) $v), 0);
    $signed = fn ($v) => ($v < 0 ? '−' : '') . '₹' . number_format(abs((float) $v), 0);
    $pct = fn ($now, $prev) => $prev > 0 ? round(($now - $prev) / $prev * 100) : null;

    $firstName = explode(' ', trim(auth()->user()->name ?? ''))[0] ?: 'there';
    $hour = (int) now()->setTimezone('Asia/Kolkata')->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $monthKept = $thisMonthIncome - $thisMonthExpense;
    $spentShare = $thisMonthIncome > 0 ? min(100, round($thisMonthExpense / $thisMonthIncome * 100)) : null;
    $allNet = $totalIncome - $totalExpense;
    $monthlyNet = array_map(fn ($i, $e) => $i - $e, $monthlyIncome, $monthlyExpense);
    $hasTrend = array_sum($monthlyIncome) + array_sum($monthlyExpense) > 0;
    $hasAnyData = $totalIncome > 0 || $totalExpense > 0;

    $score = (float) ($stability['score'] ?? 0);
    $scoreTone = $score >= 70 ? ['#10b981', 'text-emerald-700', 'Strong'] : ($score >= 40 ? ['#f59e0b', 'text-amber-700', 'Fair'] : ['#f43f5e', 'text-rose-700', 'Needs attention']);
    $catTotal = (float) $categories->sum('total');
    $catColors = ['#f43f5e', '#f59e0b', '#6366f1', '#0ea5e9', '#10b981', '#a855f7'];
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header :title="$greeting . ', ' . $firstName" eyebrow="My money" icon="fa-wallet" :stamp="false"
        description="Here’s where your money stands, from the income and expenses you’ve recorded.">
        <x-slot name="actions">
            <a href="{{ route('user.incomes.create') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-plus text-xs text-emerald-600" aria-hidden="true"></i> Add income
            </a>
            <a href="{{ route('user.expenses.create') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-minus text-xs" aria-hidden="true"></i> Add expense
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    @unless($hasAnyData)
        {{-- First-run guide --}}
        <section class="rounded-2xl border border-dashed border-zinc-300 bg-white p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-zinc-900">Let’s set up your money picture</h2>
            <p class="mt-1 text-sm text-zinc-500">Add a couple of records and this page fills in with your totals, trends and health score.</p>
            <ol class="mt-6 grid gap-3 sm:grid-cols-3">
                @foreach([
                    ['1', 'Add your income', 'Salary, freelance work or any money that comes in.', route('user.incomes.create')],
                    ['2', 'Add an expense', 'Food, bills, travel — anything you spend.', route('user.expenses.create')],
                    ['3', 'Ask the AI advisor', 'Get answers based on your own numbers.', route('user.ai.chat')],
                ] as [$n, $t, $d, $href])
                    <li><a href="{{ $href }}" class="flex h-full gap-3 rounded-xl border border-zinc-200 p-4 hover:border-zinc-300 hover:bg-zinc-50">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-xs font-semibold text-white">{{ $n }}</span>
                        <span><span class="block text-sm font-semibold text-zinc-900">{{ $t }}</span><span class="mt-0.5 block text-xs text-zinc-500">{{ $d }}</span></span>
                    </a></li>
                @endforeach
            </ol>
        </section>
    @endunless

    {{-- This month band --}}
    <section class="rounded-2xl bg-zinc-950 text-white ring-1 ring-zinc-900" aria-label="This month">
        <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-5">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ now()->format('F Y') }} · kept so far</p>
                <p class="mt-2 text-4xl font-semibold tracking-tight tabular-nums sm:text-5xl {{ $monthKept < 0 ? 'text-rose-300' : '' }}">{{ $signed($monthKept) }}</p>
                @if($spentShare !== null)
                    <div class="mt-4 max-w-sm">
                        <div class="flex justify-between text-xs text-zinc-400"><span>Spent {{ $spentShare }}% of this month’s income</span></div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full {{ $spentShare >= 90 ? 'bg-rose-400' : ($spentShare >= 70 ? 'bg-amber-400' : 'bg-emerald-400') }}" style="width: {{ $spentShare }}%"></div></div>
                    </div>
                @else
                    <p class="mt-3 text-sm text-zinc-400">{{ $thisMonthExpense > 0 ? 'You’ve spent money this month but haven’t added any income yet.' : 'No income or expenses recorded this month yet.' }}</p>
                @endif
            </div>
            <dl class="grid grid-cols-2 gap-6 border-t border-white/10 pt-6 lg:col-span-4 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                <div>
                    <dt class="text-xs text-zinc-400">Money in</dt>
                    <dd class="mt-1 text-2xl font-semibold tabular-nums text-emerald-300">{{ $inr($thisMonthIncome) }}</dd>
                    @if(($d = $pct($thisMonthIncome, $lastMonthIncome)) !== null)<dd class="mt-0.5 text-xs text-zinc-400">{{ $d >= 0 ? '+' : '−' }}{{ abs($d) }}% vs last month</dd>@endif
                </div>
                <div>
                    <dt class="text-xs text-zinc-400">Money out</dt>
                    <dd class="mt-1 text-2xl font-semibold tabular-nums text-rose-300">{{ $inr($thisMonthExpense) }}</dd>
                    @if(($d = $pct($thisMonthExpense, $lastMonthExpense)) !== null)<dd class="mt-0.5 text-xs text-zinc-400">{{ $d >= 0 ? '+' : '−' }}{{ abs($d) }}% vs last month</dd>@endif
                </div>
            </dl>
            {{-- Health score --}}
            <div class="flex items-center gap-4 border-t border-white/10 pt-6 lg:col-span-3 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                <div class="relative h-20 w-20 shrink-0">
                    <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90" aria-hidden="true">
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="{{ $scoreTone[0] }}" stroke-width="3" stroke-linecap="round" stroke-dasharray="97.4" stroke-dashoffset="{{ 97.4 - 97.4 * $score / 100 }}"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xl font-semibold tabular-nums">{{ round($score) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium">Health score</p>
                    <p class="text-xs text-zinc-400">{{ $hasAnyData ? $scoreTone[2] . ' · out of 100' : 'Add records to calculate' }}</p>
                    <a href="#score" class="mt-1 inline-block text-xs font-medium text-indigo-300 hover:text-indigo-200">How it’s worked out</a>
                </div>
            </div>
        </div>
    </section>

    {{-- KPI tiles --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Total income" :value="$inr($totalIncome)" icon="fa-arrow-trend-up" tone="emerald" :spark="$monthlyIncome" :href="route('user.incomes.index')"
            :hint="$topSource ? 'Biggest source: ' . e(\Illuminate\Support\Str::limit($topSource->source, 22)) : 'All time'" />
        <x-admin.stat label="Total expenses" :value="$inr($totalExpense)" icon="fa-arrow-trend-down" tone="rose" :spark="$monthlyExpense" :href="route('user.expenses.index')" hint="All time" />
        <x-admin.stat label="Net saved" :value="$signed($allNet)" icon="fa-piggy-bank" tone="indigo"
            :hint="$totalIncome > 0 ? round($allNet / $totalIncome * 100, 1) . '% of income kept' : 'All time'" />
        <x-admin.stat label="Emergency runway" :value="$hasAnyData ? number_format((float) ($stability['runway'] ?? 0), 1) . ' mo' : '—'" icon="fa-life-ring" tone="sky"
            hint="Months your savings would cover your usual spending" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Trend --}}
        <x-admin.card class="lg:col-span-2" title="Last 6 months" description="Income and expenses by month">
            <x-slot name="action">
                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Income</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Expenses</span>
                </div>
            </x-slot>
            <div class="relative h-64">
                <canvas id="trendChart" role="img" aria-label="Income and expenses for each of the last 6 months"></canvas>
                @unless($hasTrend)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-column" title="Nothing recorded in the last 6 months" text="Add income or an expense to see your trend." />
                @endunless
            </div>
        </x-admin.card>

        {{-- Categories --}}
        <x-admin.card title="Where your money goes" description="Expenses · last 6 months">
            <x-slot name="action"><a href="{{ route('user.reports.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Reports →</a></x-slot>
            @if($categories->isEmpty())
                <x-admin.empty icon="fa-chart-pie" title="No expenses yet" text="Your categories will show here." />
            @else
                <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-zinc-100">
                    @foreach($categories as $i => $c)<span style="width: {{ $catTotal > 0 ? $c->total / $catTotal * 100 : 0 }}%; background: {{ $catColors[$i % 6] }}"></span>@endforeach
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach($categories as $i => $c)
                        <li class="flex items-center gap-2.5 text-sm">
                            <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $catColors[$i % 6] }}"></span>
                            <span class="flex-1 truncate text-zinc-700">{{ $c->category }}</span>
                            <span class="tabular-nums text-zinc-900">{{ $inr($c->total) }}</span>
                            <span class="w-10 text-right text-xs tabular-nums text-zinc-400">{{ $catTotal > 0 ? round($c->total / $catTotal * 100) : 0 }}%</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent transactions --}}
        <x-admin.card class="lg:col-span-2" title="Recent transactions" flush>
            <x-slot name="action">
                <div class="flex gap-3 text-xs font-medium">
                    <a href="{{ route('user.incomes.index') }}" class="text-indigo-600 hover:text-indigo-700">All income</a>
                    <a href="{{ route('user.expenses.index') }}" class="text-indigo-600 hover:text-indigo-700">All expenses</a>
                </div>
            </x-slot>
            @if($recent->isEmpty())
                <x-admin.empty icon="fa-receipt" title="No transactions yet" text="Income and expenses you add will appear here." />
            @else
                <ul class="divide-y divide-zinc-100">
                    @foreach($recent as $t)
                        <li>
                            <a href="{{ $t->edit }}" class="group flex items-center gap-3 px-4 py-3 hover:bg-zinc-50/70 sm:px-5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $t->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                    <i class="fa-solid {{ $t->type === 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-zinc-900">{{ $t->title }}</p>
                                    <p class="truncate text-xs text-zinc-500">{{ $t->category }} · {{ $t->date->format('d M Y') }}@if($t->family) · Family @endif</p>
                                </div>
                                <span class="text-sm font-semibold tabular-nums {{ $t->type === 'income' ? 'text-emerald-600' : 'text-zinc-900' }}">{{ $t->type === 'income' ? '+' : '−' }}{{ $inr($t->amount) }}</span>
                                <i class="fa-solid fa-pen text-[10px] text-zinc-300 opacity-0 transition group-hover:opacity-100" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>

        <div class="space-y-6">
            {{-- Health score breakdown --}}
            <x-admin.card id="score" title="Health score breakdown" description="{{ now()->year }} so far · 0–100">
                @php
                    $parts = [
                        ['Savings rate', 40, min(100, max(0, (float) ($stability['savings_rate'] ?? 0)))],
                        ['Spending control', 30, max(0, 100 - min(100, (float) ($stability['expense_ratio'] ?? 100)))],
                        ['Steady income', 20, (float) ($stability['consistency_score'] ?? 0)],
                        ['Emergency runway', 10, (float) ($stability['emergency_score'] ?? 0)],
                    ];
                @endphp
                <ul class="space-y-3">
                    @foreach($parts as [$label, $weight, $value])
                        <li>
                            <div class="flex justify-between text-sm"><span class="text-zinc-700">{{ $label }} <span class="text-xs text-zinc-400">· {{ $weight }}%</span></span><span class="tabular-nums text-zinc-900">{{ round($value) }}/100</span></div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-indigo-500" style="width: {{ $value }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>

            {{-- Goals --}}
            <x-admin.card title="Savings goals">
                <x-slot name="action"><a href="{{ route('user.ai.chat') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Add with AI →</a></x-slot>
                @if($goals->isEmpty())
                    <p class="text-sm text-zinc-500">No goals yet. Ask the AI advisor to set one up — a trip, an emergency fund, a new laptop.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($goals as $g)
                            @php $gp = $g->target_amount > 0 ? min(100, round($g->current_amount / $g->target_amount * 100)) : 0; @endphp
                            <li>
                                <div class="flex justify-between gap-3 text-sm"><span class="truncate font-medium text-zinc-800">{{ $g->title }}</span><span class="shrink-0 tabular-nums text-zinc-500">{{ $inr($g->current_amount) }} / {{ $inr($g->target_amount) }}</span></div>
                                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $gp }}%"></div></div>
                                @if($g->target_date)<p class="mt-1 text-xs text-zinc-500">By {{ $g->target_date->format('d M Y') }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            @if($unreadAlerts > 0)
                <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 hover:bg-amber-100">
                    <i class="fa-solid fa-bell" aria-hidden="true"></i>
                    <span class="flex-1">{{ $unreadAlerts }} unread {{ \Illuminate\Support\Str::plural('alert', $unreadAlerts) }}</span>
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('trendChart');
    if (!el || typeof Chart === 'undefined') return;
    const inr = v => '₹' + Number(v).toLocaleString('en-IN');
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [
                { label: 'Income', data: @json($monthlyIncome), backgroundColor: '#10b981', borderRadius: 4, maxBarThickness: 26 },
                { label: 'Expenses', data: @json($monthlyExpense), backgroundColor: '#f43f5e', borderRadius: 4, maxBarThickness: 26 },
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
